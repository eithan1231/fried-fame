
/**
* Class for managing some security. Mostly Self-XSS.
*/
class security
{
  static init()
  {
    if(!ff_config.debug) {
      setInterval(() => {
        if(ff_config.debug) {
          return;// You can enter debug mode after loading page.
        }
        security.securityLoop();
      }, 5000);
      security.securityLoop();
    }
  }

  static securityLoop()
  {
    for (let i = 0; i < 10; i++) {
      // Trying to clear console... it's ugly, but it works.
      console.log("\n\n");
      console.log("\n");
    }
    console.log(
      '%cWarning message',
      'font: 5em sans-serif; color: red; font-weight: bold;'
    );
    console.log(
      '%cIf you have been told to paste something here to \'Hack an account\', or \'get a free subscription\', this IS A SCAM!',
      'font: 2em sans-serif; color: red; font-weight: bold;'
    );
    console.log(
      '%cFor more infroamtion, see here https://en.wikipedia.org/wiki/Self-XSS',
      'font: 2em sans-serif; color: red; font-weight: bold;'
    );
  }
}

/**
* Class for managing the Sidebar
*/
class sidebar
{
  static init()
  {
    sidebar.sidebarElement = document.getElementById('sidebar');
    if(!sidebar.sidebarElement) {
      return;
    }
    sidebar.handleMarginsAndWidths();
    window.addEventListener('resize', sidebar.handleWindowResize);
  }

  static handleMarginsAndWidths()
  {
    if(sidebar.sidebarElement.hidden) {
      document.documentElement.style.setProperty('--sidebar-width', '0px');
    }
    else {
      document.documentElement.style.setProperty('--sidebar-width', ff_config.sidebarWidth);
    }
  }

  static handleWindowResize()
  {
    if(window.innerWidth < 1024) {
      if(!sidebar.sidebarElement.hidden) {
        sidebar.toggle();
      }
    }
  }

  static toggle()
  {
    if(!sidebar.sidebarElement) {
      return;
    }

    sidebar.sidebarElement.hidden = !sidebar.sidebarElement.hidden;

    cookie.set(
      ff_config.sidebarVisibleCookieName,
      sidebar.sidebarElement.hidden
    );

    sidebar.handleMarginsAndWidths();

    return false;
  }
}

/**
* Cookie class for interfacing with cookies.
*/
class cookie
{
  static set(name, value, seconds)
  {
    let expires = '';
    let date;
    if (seconds) {
      date = new Date();
      date.setTime(date.getTime() + (seconds * 1000));
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(value.toString())  + expires + '; path=/';
    return cookie.exists(name);
  }

  static get(key)
  {
    let cookieSignature = key + '=';
    let cookies = document.cookie.split(';');
    for (let cookie of cookies) {
      if(cookie.substr(0, cookieSignature.length) == cookieSignature) {
        return decodeURIComponent(cookie.substr(cookieSignature.length));
      }
    }
    return false;
  }

  static exists(key)
  {
    return cookie.get(key) !== false;
  }
}

/**
* Class for managing Administrative Lingual phrases.
*/
class adminLanguage
{
  static automaticHandleOutdated(elem)
  {
    adminLanguage.setPhrase(
      elem.dataset.phrasename,
      elem.dataset.phraselanguage,
      elem.dataset.phraserevision,
      elem.parentElement.getElementsByTagName('textarea')[0].value,
      function(state) {
        if(state == 'begin') {
          elem.disabled = true;
        }
        if(state == 'success') {
          ff_custom.killElement(elem.parentElement.parentElement.parentElement)
        }
        if(state == 'error') {
          alert(`Failed to update ${elem.dataset.phrasename}`);
          elem.disabled = false;
        }
      }
    )
  }

  static setPhrase(phraseName, phraseLanguage, phraseRevision, phraseBody, callback = null)
  {
    if(!callback) {
      // Empty function
      callback = e => { };
    }
    callback('begin');

    let data = '';
    data += 'phrase_name=' + encodeURIComponent(phraseName) + '&';
    data += 'phrase_language=' + encodeURIComponent(phraseLanguage) + '&';
    data += 'phrase_revision=' + encodeURIComponent(phraseRevision) + '&';
    data += 'phrase_body=' + encodeURIComponent(phraseBody);

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if(this.readyState == 4) {
        callback(this.status == 200 ? 'success' : 'error');
      }
    };
    xhttp.open('POST', ff_getPostRoute('setphrase'))
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(data);
  }
}

/**
* Interface for payments. Basically just for getting that status of a payment.
*/
class payment
{
  static getStatus(token, callback)
  {
    let data = 'token=' + encodeURIComponent(token);

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if(this.readyState == 4) {
        if(this.status == 200) {
          let responseData = JSON.parse(this.responseText);
          if(responseData.cmd) {
            callback(null, responseData.cmd.toLowerCase(), responseData);
          }
          else {
            callback(new Error('bad response'));
          }
        }
        else {
          callback(new Error('bad status'));
        }
      }
    };
    xhttp.open('POST', ff_getPostRoute('paymentstate'))
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(data);
  }
}

/**
* Automated settings. An example would be automatically calculating time offset
* and sending to the server so that it doesn't need to calculate it manually.
*/
class autosettings
{
  static init()
  {
    autosettings.configureTimeDifference();
  }

  /**
  * Gets and validates the time difference auto setting. If it's wrong,
  * re-calculate it and set it on the server.
  */
  static configureTimeDifference()
  {
    if(!ff_config || !ff_config.autoSettings || (typeof ff_config.autoSettings.time_difference != 'number')) {
      // time difference not found
      return;
    }

    const currentOffset = ff_getTimezoneOffset();

    if(ff_config.autoSettings.time_difference != currentOffset) {
      ff_debugPrint("Difference in time-zone calculated. Synchronizing change with server. Offset: " + currentOffset);
      autosettings.setSetting('time_difference', currentOffset);
    }
  }

  static setSetting(name, value, callback = null)
  {
    if(callback === null) {
      callback = (err) => {
        ff_debugPrint(err
          ? `Auto Settings failed to set ${name} to ${value}`
          : `Auto Settings updated ${name} to ${value}`
        );
      };
    }
    let data = 'settings['+ encodeURIComponent(name) +']='+ encodeURIComponent(value) +'&';

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if(this.readyState == 4) {
        if(this.status == 200) {
          callback(null);
        }
        else {
          callback(new Error(`Unexpected Status: ${this.status}`));
        }
      }
    };
    xhttp.open('POST', ff_getPostRoute('setsettings'))
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(data);
  }
}

function ff_getPostRoute(action)
{
  if(typeof ff_config.route_post != 'string') {
    return null;
  }

  return ff_config.route_post.replace('__action__', encodeURIComponent(action));
}

function ff_killElement(element)
{
  element.parentElement.removeChild(element);
}

/**
* Returns timezone difference between UTC and local time. Is measured in seconds.
*/
function ff_getTimezoneOffset()
{
  // The reason I'm converting this from a negative to posible (vise versa), is
  // because I would like to add the offset, not subtract it. Adding makes more
  // sense to me.
  return -((new Date()).getTimezoneOffset() * 60);
}

function ff_debugPrint(...args)
{
  ff_custom.debugTrace.push([ ...args ]);
  if(window.ff_config.debug) {
    console.log(...args);
  }
}

/**
* Error Handler
*/
function ff_errorHandler(e)
{
  //alert('An internal error occured.\n\nIf you\'re a technical person, go to the console and report the error via a suppor ticket. Otherwise try again.\n\nSorry for the inconvenience');
  console.error(e);
}

/**
* Enters a debug state.
*/
function ff_enterDebug()
{
  if(window.ff_config.debug) {
    return;
  }

  for (const log of ff_custom.debugTrace) {
    console.log(...log);
  }
  window.ff_config.debug = true;
}

window.ff_custom = {
  debugTrace: [],
  sidebar,
  security,
  autosettings,
  admin: {
    language: adminLanguage
  },

  getPostRoute: ff_getPostRoute,
  killElement: ff_killElement,
  getTimezoneOffset: ff_getTimezoneOffset,
  debugPrint: ff_debugPrint,
  enterDebug: ff_enterDebug,
};

window.addEventListener('load', () => {
  ff_custom.sidebar.init();
  ff_custom.security.init();
  ff_custom.autosettings.init();
});

window.addEventListener('error', ff_errorHandler);

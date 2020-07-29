/**
* Modified version of this, check out the original.
* https://github.com/jaredreich/this
*/
var ff_pell = new function() {
  this.defaultParagraphSeparatorString = 'defaultParagraphSeparator';
  this.formatBlock = 'formatBlock';
  this.addEventListener = function(parent, type, listener) {
    parent.addEventListener(type, listener);
  };
  this.appendChild = function(parent, child) {
    parent.appendChild(child);
  };
  this.queryCommandState = function(command) {
    document.queryCommandState(command);
  };
  this.queryCommandValue = function(command) {
    document.queryCommandValue(command);
  };

  this.exec = function(command, value = null) {
    document.execCommand(command, false, value);
  };

  this.defaultActions = {
    bold: {
      icon: '<b>B</b>',
      title: 'Bold',
      state: function() {
        return ff_pell.queryCommandState('bold');
      },
      result: function() {
        return ff_pell.exec('bold');
      }
    },
    italic: {
      icon: '<i>I</i>',
      title: 'Italic',
      state: function() {
        return ff_pell.queryCommandState('italic');
      },
      result: function() {
        return ff_pell.exec('italic');
      }
    },
    underline: {
      icon: '<u>U</u>',
      title: 'Underline',
      state: function() {
        return ff_pell.queryCommandState('underline');
      },
      result: function() {
        return ff_pell.exec('underline');
      }
    },
    strikethrough: {
      icon: '<strike>S</strike>',
      title: 'Strike-through',
      state: function() {
        ff_pell.queryCommandState('strikeThrough');
      },
      result: function() {
        return ff_pell.exec('strikeThrough');
      }
    },
    heading1: {
      icon: '<b>H<sub>1</sub></b>',
      title: 'Heading 1',
      result: function() {
        return ff_pell.exec(ff_pell.formatBlock, '<h1>');
      }
    },
    heading2: {
      icon: '<b>H<sub>2</sub></b>',
      title: 'Heading 2',
      result: function() {
        return ff_pell.exec(ff_pell.formatBlock, '<h2>');
      }
    },
    paragraph: {
      icon: '&#182;',
      title: 'Paragraph',
      result: function() {
        return ff_pell.exec(ff_pell.formatBlock, '<p>');
      }
    },
    quote: {
      icon: '&#8220; &#8221;',
      title: 'Quote',
      result: function() {
        return ff_pell.exec(ff_pell.formatBlock, '<blockquote>');
      }
    },
    olist: {
      icon: '&#35;',
      title: 'Ordered List',
      result: function() {
        return ff_pell.exec('insertOrderedList');
      }
    },
    ulist: {
      icon: '&#8226;',
      title: 'Unordered List',
      result: function() {
        return ff_pell.exec('insertUnorderedList');
      }
    },
    code: {
      icon: '&lt;/&gt;',
      title: 'Code',
      result: function() {
        return ff_pell.exec(ff_pell.formatBlock, '<pre>');
      }
    },
    line: {
      icon: '&#8213;',
      title: 'Horizontal Line',
      result: function() {
        return ff_pell.exec('insertHorizontalRule');
      }
    },
    link: {
      icon: '&#128279;',
      title: 'Link',
      result: function() {
        let url = window.prompt('Enter the link URL');
        if (url) {
          return ff_pell.exec('createLink', url);
        }
      }
    }/*,
    image: {
      icon: '&#128247;',
      title: 'Image',
      result: function() {
        let url = window.prompt('Enter the image URL');
        if (url) {
          return ff_pell.exec('insertImage', url);
        }
      }
    }*/
  };

  this.defaultClasses = {
    actionbar: 'pell-actionbar',
    button: 'pell-button',
    content: 'pell-content',
    selected: 'pell-button-selected'
  };

  this.init = function(settings) {
    let actions = settings.actions
      ? settings.actions.map(function(action) {
        if (typeof action === 'string') {
          return ff_pell.defaultActions[action];
        }
        else if (ff_pell.defaultActions[action.name]) {
          return { ...ff_pell.defaultActions[action.name], ...action };
        }
        return action;
      })
      : Object.keys(ff_pell.defaultActions).map(function(action) {
        return ff_pell.defaultActions[action];
      });

    let classes = {
      ...ff_pell.defaultClasses,
      ...settings.classes
    };

    let defaultParagraphSeparator = settings[ff_pell.defaultParagraphSeparatorString] || 'div';

    let actionbar = document.createElement('div');
    actionbar.className = classes.actionbar;
    settings.element.innerHTML = '';// Removing existing content.
    ff_pell.appendChild(settings.element, actionbar);

    let content = settings.element.content = document.createElement('div');
    content.contentEditable = true;
    content.className = classes.content;
    content.oninput = function({ target: { firstChild } }) {
      if (firstChild && firstChild.nodeType === 3) {3
        ff_pell.exec(ff_pell.formatBlock, `<${defaultParagraphSeparator}>`);
      }
      else if (content.innerHTML === '<br>') {
        content.innerHTML = '';
      }
      settings.onChange(content.innerHTML);
    }
    content.onkeydown = function(event) {
      if (event.key === 'Tab') {
        event.preventDefault();
      } else if (event.key === 'Enter' && ff_pell.queryCommandValue(ff_pell.formatBlock) === 'blockquote') {
        setTimeout(function() {
          ff_pell.exec(ff_pell.formatBlock, `<${defaultParagraphSeparator}>`);
        }, 0);
      }
    };
    ff_pell.appendChild(settings.element, content);

    actions.forEach(function(action) {
      let button = document.createElement('button');
      button.className = classes.button;
      button.innerHTML = action.icon;
      button.title = action.title;
      button.setAttribute('type', 'button');
      button.onclick = function() {
        action.result();
        content.focus();
      };

      if (action.state) {
        let handler = function() {
          button.classList[action.state() ? 'add' : 'remove'](classes.selected);
        };
        ff_pell.addEventListener(content, 'keyup', handler)
        ff_pell.addEventListener(content, 'mouseup', handler);
        ff_pell.addEventListener(button, 'click', handler);
      }

      ff_pell.appendChild(actionbar, button);
    });

    if (settings.styleWithCSS) {
      ff_pell.exec('styleWithCSS');
    }
    ff_pell.exec(ff_pell.defaultParagraphSeparatorString, defaultParagraphSeparator);

    return settings.element;
  };
}();

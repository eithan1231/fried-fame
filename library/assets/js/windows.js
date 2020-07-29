const {remote, ipcRenderer} = require('electron');
const primaryWindow = remote.getCurrentWindow();
const config = remote.getGlobal('config');
const url = require('url');

const windows = {
  close: function() {
    primaryWindow.close();
  },

  minimize: function() {
    primaryWindow.minimize();
  },

  // Starts ICP listener, and things as such.
  init: function() {
    windows.makeDragable('navbar');
  },

  startConnection: async function(node_id) {
		return new Promise(async (resolve, reject) => {

		});
  },

  terminateConnection: async function(aElement) {
		return new Promise(async (resolve, reject) => {

		});
  },

  getStatus: async function(aElement) {
		return new Promise(async (resolve, reject) => {

		});
  },

  // Makes an element dragable, so uou can move it over the screen, and such.
  makeDragable: function(id) {
    let element = document.getElementById(id);
    let isDown = false;
    let offset = {
			x: -1,
			y: -1
		};

    element.addEventListener('mousedown', function(e) {
      if(e.which === 1) {
        isDown = true;
      }

      offset.x = e.clientX;
      offset.y = e.clientY;
    });

    element.addEventListener('mouseup', (e) => {
      if(e.which === 1) {
        isDown = false;
      }
    });

		element.addEventListener('mousemove', (e) => {
			if(!isDown) {
        return;
      }

      primaryWindow.setPosition(
        e.screenX - offset.x,
        e.screenY - offset.y
      );
		});
  },

  openUrlElsewhere: function(aElement) {
    ipcRenderer.send('ff-openurl', aElement.href);
    return false;
  }
};

window.addEventListener('load', windows.init);

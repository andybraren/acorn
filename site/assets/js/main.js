// Main Acorn JS file
// by Andy Braren

// console.log('Acorn debug mode is turned on');
// VAR_GoogleAnalytics

window.onload = function() {
  checkTheme();
  checkFontSize();
  checkFontFamily();
  
  videoEmbed();
  heroImages();
  enableModalButtons();
  
  checkStripe();
  
  /* Activate bLazy */
  var bLazy = new Blazy({ 
      // selector: 'img', // all images
      offset: 500
  });
  
  progressNav();
    
  /* Activate headroom */
  var myElement = document.querySelector("header"); // grab an element
  var headroom  = new Headroom(myElement); // construct an instance of Headroom, passing the element
  headroom.init(); // initialise
  
  var topbutton = document.getElementsByClassName('toc-top');
  if (topbutton != null) {
    for (var i=0; i<topbutton.length; i++) {
      topbutton[i].addEventListener('click', function(event) { // Scroll to top, and replace the URL
        window.scrollTo(0,0);
        history.replaceState({}, document.title, window.location.href.split('#')[0]);
      });
    }
  }
  
  // TOC web history cleaner
  // A JS enhancement that prevents every TOC click from being added to web history
  // This makes the back button behave more expectedly, although an argument could be made otherwise
  // Falls back to default HTML behavior
  var tocitems = document.getElementsByClassName('toc-items');
  if (tocitems != null) {
    for (var i=0; i<tocitems.length; i++) {
      tocitems[i].addEventListener('click', function(event) {
        if (event.target && event.target.nodeName == "A") {
          event.preventDefault();
          var baseUrl = window.location.href.split('#')[0];
          var anchor = event.target.href.split('#')[1];
          window.location.replace( baseUrl + '#' + anchor );
        }
      });
    }
  }
  
  // Update signup modal color on color change
  var colorfield = document.getElementById('signup-color');
  if (colorfield != null) {
    var signupmodal = document.getElementById('modal-signup');
    colorfield.onchange = function() {
      signupmodal.className = "modal visible " + colorfield.value;
    };
  }
  
  /* Edit button behavior */
  var editbutton = document.getElementById('button-edit');
  if (editbutton != null) {
    editbutton.addEventListener('click', toggleEdit);
  }
  

  
  
}

clickcount = 0;
function toggleEdit() {
  
  // Reveal all editor UI components
  
  if (clickcount == 0) {
    let editorComponents = document.querySelectorAll('[data-editor="hidden"]');
    for (var i = 0; i < editorComponents.length; i++) {
      editorComponents[i].setAttribute('data-editor', 'visible');
    }
  } else {
    let editorComponents = document.querySelectorAll('[data-editor="visible"]');
    for (var i = 0; i < editorComponents.length; i++) {
      editorComponents[i].setAttribute('data-editor', 'hidden');
    }
  }
  
  /* Avoiding anonymous functions, so that the eventListener can be removed, while still passing parameters to the function
      https://toddmotto.com/avoiding-anonymous-javascript-functions/
      Turns out that bind can't be used, because using it creates a sort of virtual function every time it's called, which makes it different
      Supposedly you can set it to a separate variable and then add/remove the click listener referencing that variable, but doesn't quite work
      This seems to work. Creating top-level function names that can have their listener added and removed consistently.
  */
  
  var uploadForm = document.getElementById('upload-form');
  if (clickcount == 0) {
    clickInputWhenClicked(uploadForm.querySelector('[name="hero"]'), document.getElementById('hero'));
    if (document.getElementById('icon'))   { clickInputWhenClicked(uploadForm.querySelector('[name="icon"]'),   document.getElementById('icon')); }
    if (document.getElementById('avatar')) { clickInputWhenClicked(uploadForm.querySelector('[name="avatar"]'), document.getElementById('avatar')); }
  } else {
    dontClickInputWhenClicked(uploadForm.querySelector('[name="hero"]'), document.getElementById('hero'));
    if (document.getElementById('icon'))   { dontClickInputWhenClicked(uploadForm.querySelector('[name="icon"]'),   document.getElementById('icon')); }
    if (document.getElementById('avatar')) { dontClickInputWhenClicked(uploadForm.querySelector('[name="avatar"]'), document.getElementById('avatar')); }
  }
  
  function clickInputWhenClicked(input, activator) {
    window[input.name + 'UploadFunction'] = function() { inputClick(input) }; // Each input is given its own global function name, used later
    activator.addEventListener('click', window[input.name + 'UploadFunction']);
  }
  
  function dontClickInputWhenClicked(input, activator) {
    activator.removeEventListener('click', window[input.name + 'UploadFunction']); // Each input's function is removed
  }
  
  
  
  
  
  
  
  var editbutton = document.getElementById('button-edit');
  
  /* Hero and icon image editing */
  var formupload = document.getElementById('upload-form');


  var formSettings = document.getElementById('form-settings');
  var formSettingsVisibility = document.getElementById('visibility');
  
  if(clickcount == 0) {
    document.body.classList.toggle('editing');
    replaceFigures(); // from editor.js, need to finish before ignition.edit
    editor.ignition().edit();
    addFileTool();
    toggleItems(clickcount);
    itemDeleteButtons();
    itemConfirmButtons();
    Blazy();
    editbutton.innerHTML = 'Save';
    clickcount = 1;
  } else {
    checkEmptyWidgets()
    document.body.classList.toggle('editing');
    toggleItems(clickcount);
    editbutton.innerHTML = 'Edit';
    editor.ignition().confirm();
    savePage()
    videoEmbed();
    clickcount = 0;
  }

  
  var color = document.getElementById('color');
  if (color != null) {
    currentColor = color.options[color.selectedIndex].value;
    color.onchange = function() {
      document.body.classList.remove(currentColor);
      document.body.classList.add(color.options[color.selectedIndex].value);
      currentColor = color.options[color.selectedIndex].value;
    };
  }
  

}


/*--------------------------------------------------
  Upload Functions
  - One area for all uploading-related functions
--------------------------------------------------*/

function inputClick(input) {
  input.click();
  input.onchange = function() {
    upload(input, input.name);
  };
}

// provide the input field to upload, along with what type it is
async function upload(input, name) {
  
  var data = new FormData();
  data.append('page', window.location.pathname);
  data.append('type', name);
  
  // provide the files to upload
  for (var i = 0; i < input.files.length; i++) {
    data.append('files', input.files[i], input.files[0].name);
  }
  
  // send the data
  fetch('upload', {
    method: 'POST',
    body: data,
  })
  .then(function(response) {
    return response.json(); // Process the JSON that is eventually returned
  })
  .then(function(data) {
  	window[name + 'Uploaded'](data); // Pass the data into one of the functions below
  })
  /* For some reason, adding this catch makes it end too early and it returns the error
  .catch(function(err) {
  	console.log('There was an error uploading the file');
  });
  */
}

function iconUploaded(data) {
  document.querySelector('#icon-img').src = data.fileurl;
}

function avatarUploaded(data) {
  document.querySelector('#avatar-img').src = data.fileurl;
}

function heroUploaded(data) {
  var heroAdd = document.querySelector('#hero-add');
  if (heroAdd != undefined) {
    html = '<figure><img src="' + data.fileurl + '"></img></figure>';
    heroAdd.insertAdjacentHTML('afterend', html);
    heroAdd.parentNode.removeChild(heroAdd);
  } else {
    image = document.getElementById('hero').getElementsByTagName('IMG')[0];
    image.removeAttribute('class');
    image.parentNode.removeAttribute('style');
    image.src = data.fileurl;
  }
}







/*--------------------------------------------------
  Theme Setting
  - Adds a "night" class to the body and stores the night setting within localstorage
--------------------------------------------------*/

/*
  Rewrite this. Store the theme name within a data attribute and have JS either
  add that to storage and apply it to body or remove it. Simpler and more extensible.
*/

var nightMode = document.getElementsByClassName('theme-night');
if (nightMode != null) {
  for (var i=0; i<nightMode.length; i++) {
    nightMode[i].addEventListener('click', function(event) {
      toggleTheme();
    });
  }
}

function toggleTheme() {
  let theme = localStorage.getItem('theme');
  if (theme) {
    document.body.classList.remove(theme);
    localStorage.removeItem('theme');
    document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: Off';
  } else {
    localStorage.setItem('theme', 'night');
    document.body.classList.add('night');
    document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: On';
  }
}

function checkTheme() {
  let theme = localStorage.getItem('theme');
  if (theme) {
    document.body.classList.add(theme);
    document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: On';
  }
}

/*--------------------------------------------------
  Font Family Setting
  - Adjusts the font family used throughout the interface
--------------------------------------------------*/

let fontFamily = document.getElementsByClassName('font-family');
if (fontFamily != null) {
  for (var i=0; i<fontFamily.length; i++) {
    fontFamily[i].addEventListener('click', function(event) {
      toggleFontFamily();
    });
  }
}

function toggleFontFamily() {
  let fontFamily = localStorage.getItem('fontFamily');
  if (fontFamily) {
    //document.body.classList.remove(fontFamily);
    document.documentElement.classList.remove('dyslexic');
    localStorage.removeItem('fontFamily');
    document.getElementsByClassName('font-family')[0].innerHTML = 'Dyslexia: Off';
  } else {
    localStorage.setItem('fontFamily', 'dyslexic');
    //document.body.classList.add('dyslexic');
    document.documentElement.classList.add('dyslexic');
    document.getElementsByClassName('font-family')[0].innerHTML = 'Dyslexia: On';
  }
}

function checkFontFamily() {
  let fontFamily = localStorage.getItem('fontFamily');
  if (fontFamily) {
    //document.body.classList.add(fontFamily);
    document.documentElement.classList.add(fontFamily);
    document.getElementsByClassName('font-family')[0].innerHTML = 'Dyslexia: On';
  }
}

/*--------------------------------------------------
  Font Size Setting
  - Adjusts the top-level font size and stores the setting within localstorage
--------------------------------------------------*/

/* Set click listeners for each of the font-related buttons, wherever they may be */
var fontIncrease = document.getElementsByClassName('font-increase');
if (fontIncrease != null) {
  for (var i=0; i<fontIncrease.length; i++) {
    fontIncrease[i].addEventListener('click', function(event) {
      setFontSize(+1);
    });
  }
}
var fontDecrease = document.getElementsByClassName('font-decrease');
if (fontDecrease != null) {
  for (var i=0; i<fontDecrease.length; i++) {
    fontDecrease[i].addEventListener('click', function(event) {
      setFontSize(-1);
    });
  }
}
var fontReset = document.getElementsByClassName('font-reset');
if (fontReset != null) {
  for (var i=0; i<fontReset.length; i++) {
    fontReset[i].addEventListener('click', function(event) {
      setFontSize(null);
    });
  }
}

function setFontSize(increment) {
  var currentSize = Number(window.getComputedStyle(document.documentElement).getPropertyValue('font-size').replace('px',''));
  var newSize = currentSize + increment;
  if (increment != null && newSize != initialSize) {
    document.documentElement.style.fontSize = newSize + 'px';
    document.getElementsByClassName('font-reset')[0].innerHTML = newSize;
    localStorage.setItem('fontSize', newSize);
  } else {
    document.documentElement.style.fontSize = null;
    document.getElementsByClassName('font-reset')[0].innerHTML = initialSize;
    localStorage.removeItem('fontSize');
  }  
}

function checkFontSize() {
  initialSize = Number(window.getComputedStyle(document.documentElement).getPropertyValue('font-size').replace('px',''));
  let fontSize = localStorage.getItem('fontSize');
  if (fontSize) {
    document.getElementsByClassName('font-reset')[0].innerHTML = fontSize;
    document.documentElement.style.fontSize = localStorage.getItem('fontSize') + 'px';
  }
}

/*--------------------------------------------------
  Search Box
  - Toggles the search box
--------------------------------------------------*/

var search = document.getElementById('search-box');
if (search != null) {
  
  search.addEventListener('focus', function(event) {
    document.getElementById('subnavigation').classList.toggle('searching');
  });
  
  search.addEventListener('blur', function(event) {
    document.getElementById('subnavigation').classList.toggle('searching');
  });
  
}




















var allButtons = document.querySelectorAll('button');
for (var i = 0; i < allButtons.length; i++) {
  
  allButtons[i].addEventListener('click', function(event) {
        
    // Add a class to the button, or the nearest button ancestor
    if (event.target.tagName != 'BUTTON') {
      event.target.closest('button').classList.add('focused');
      document.firstClick = event.target.closest('button');
    } else {
      event.target.classList.add('focused');
      document.firstClick = event.target;
    }
    
    document.addEventListener('mousedown', closeOnOutsideClick);
  }, false);
  
}


/* http://stackoverflow.com/questions/2234979/how-to-check-in-javascript-if-one-element-is-contained-within-another */
function isDescendant(parent, child) {
  var node = child.parentNode;
  while (node != null) {
    if (node == parent) {
      return true;
    }
    node = node.parentNode;
  }
  return false;
}

function closeOnOutsideClick(event) {
  
  var firstClick = document.firstClick; // The first clicked element
  var newClick = event.target;          // The newly-clicked element
  
  if (!isDescendant(firstClick, newClick)) {
    //console.log('outside click');
    firstClick.classList.remove('focused');
    document.removeEventListener('mousedown', closeOnOutsideClick);
  }
  
}










function deletePage() {
  data = new FormData();
  data.append('page', window.location.pathname);
  var request = new XMLHttpRequest();
  request.addEventListener('readystatechange', onStateChange);
  request.open('POST', 'delete', true);
  request.send(data);
  console.log('Page deleted');
}

var deleteButtons = document.querySelectorAll('[data-action="delete"]');
if (deleteButtons) {
  for (var i = 0; i < deleteButtons.length; i++) {
    deleteButtons[i].addEventListener('click', function() {
      deletePage();
    }, false);
  }
}



function openModal(modalname, target) {
  
  // Set variables
  var container = document.querySelector('#modals');
  var backdrop  = document.querySelector('#modal-backdrop');
  var modal     = document.querySelector('#modal-' + modalname);
  
  // Prevent the page from jumping to the top
  // This solution won't work with a strict Content Security Policy
  // http://stackoverflow.com/questions/8701754/just-disable-scroll-not-hide-it
  
  // Close any open modals
  var modals = document.querySelector('#modals').children;
  if (modals) {
    for (var i = 0; i < modals.length; i++) {
      if (modals[i].classList.contains('visible')) {
        closeModal(modals[i]);
      }
    }
  }
  
  // Replace modal content (if needed, like for Nav Items)
  if (modal.id == 'modal-navitem') {
    modal.querySelector('form').reset();
    modal.querySelector('[name="title"]').value = target.innerText;
    modal.querySelector('[name="url"]').value   = target.getAttribute('data-href');
    if (target.nextElementSibling && target.nextElementSibling.classList.contains('subtitle')) {
      modal.querySelector('[name="subtitle"]').value = target.nextElementSibling.innerHTML;
    }
  }
  // Update nav item
  var button = modal.querySelector('[data-action="updatenav"]');
  if (button) {
    
    function updateItem() {
      target.innerText = modal.querySelector('[name="title"]').value;
      target.setAttribute('data-href', modal.querySelector('[name="url"]').value);
      
      if (target.nextElementSibling && target.nextElementSibling.classList.contains('subtitle')) {
        target.nextElementSibling.innerHTML = modal.querySelector('[name="subtitle"]').value;
      } else if (modal.querySelector('[name="subtitle"]').value != '') {
        html = '<div class="subtitle">' + modal.querySelector('[name="subtitle"]').value + '</div>';
        target.insertAdjacentHTML('afterend', html);
      }
      
      closeModal(modal);
      button.removeEventListener('click', updateItem);
    }
    
    button.addEventListener('click', updateItem, false);
    
    // add sync between subnavigation and active mainnavigation nodes
    
  }
  // Delete nav item
  var button2 = modal.querySelector('[data-action="deletenav"]');
  if (button2) {
    button2.addEventListener('click', function() {
      target.parentNode.removeChild(target);
      closeModal(modal);
    }, false);
  }
  
  // Show the modal
  if (!modal.classList.contains('top')) {
    document.body.classList.add('noscroll');
  }
  container.classList.add('visible');
  modal.classList.add('visible');
  
  // Focus the modal
  lastFocus = document.activeElement; // bookmark where the user was focused
  modal.focus();
  
  // Close the modal when the backdrop is clicked
  backdrop.addEventListener('click', function(event) {
    closeModal(modal);
  }, false);
  
  // Close the modal when any of its close buttons are clicked
  var closeButtons = modal.querySelectorAll('[data-action="close"]');
  if (closeButtons) {
    for (var i = 0; i < closeButtons.length; i++) {
      closeButtons[i].addEventListener('click', function() {
        closeModal(modal);
      }, false);
    }
  }
  
}

function closeModal(modal) {
  var container = document.querySelector('#modals');
  
  // Hide modal
  document.body.classList.remove('noscroll');
  container.classList.remove('visible');
  modal.classList.remove('visible');
  
  // Unfocus the modal
  lastFocus.focus();
}

function enableModalButtons() {
  
  var openButtons = document.querySelectorAll('[data-modal]');
  if (openButtons) {
    for (var i = 0; i < openButtons.length; i++) {
      openButtons[i].addEventListener('click', function(event) {
        openModal(this.dataset.modal, event.target);
      }, false);
    }
  }
}

if (window.location.href.indexOf('login:failed') != -1) {
  openModal('login');
  enableModalButtons();
}

if (window.location.href.indexOf('forgot:success') != -1) {
  openModal('forgot');
  enableModalButtons();
}

if (window.location.href.indexOf('resetkey:') != -1) {
  openModal('reset');
  enableModalButtons();
}

if (window.location.href.indexOf('reset:success') != -1) {
  openModal('reset');
  enableModalButtons();
}















function addFileTool() {
  
  // Forcefully insert the new tool
  var prev = document.getElementsByClassName('ct-tool--video')[0];
  html = '<div class="ct-tool ct-tool--file ct-tool--disabled tool-file" data-ct-tooltip="File"></div>';
  prev.insertAdjacentHTML('afterend', html);
  
  var tool = document.getElementsByClassName('tool-file')[0];
  var focus = document.getElementsByClassName('ce-element--focused')[0];
  tool.addEventListener('click', function(event) {
    
    // Show file dialog and upload the file to the server
    var input = document.getElementById('fileToUpload')
    input.click();
    input.onchange = function() {
      var data = new FormData();

      var files = input.files;
      for (var i = 0; i < files.length; i++) {
        var file = files[i];
        data.append('files', file, file.name);
      }
      data.append('page', window.location.pathname);
      
      var request = new XMLHttpRequest();
      request.open('POST', 'upload', true);
      request.onload = function () {
        if (request.status === 200) { // File uploaded
          response = JSON.parse(this.response);
          insertFile(response.filename, response.fileurl, response.extension);
        } else {
          //alert('An error occurred! Contact Andy Braren for help.');
        }
      };
      request.send(data);
      
    };
    
    function insertFile(filename, fileurl, extension) {
      
      if (extension == 'jpg' ||
          extension == 'png' ||
          extension == 'gif') {
        extension = 'img';
      }
      
      html = '<p class="ce-element ce-element--type-text"><a href="' + fileurl + '" class="file-' + extension + '" data-filename="' + filename + '">' + filename + '</a></p>';
        
      // For whenever ContentTools is replaced
      // focus.insertAdjacentHTML('afterend', html);
      // var newelement = focus.nextSibling;
      // focus.parentNode.removeChild(focus);
      
      // ContentTools implementation
      // This is necessary to make the file's block editable after insertion
      // https://github.com/GetmeUK/ContentTools/issues/201
      selectedElm = ContentEdit.Root.get().focused();
      p = new ContentEdit.Text('p', {}, html);
      selectedElm.parent().attach(p, selectedElm.parent().children.indexOf(selectedElm) + 1);
      p.focus();
      
      // New paragraph below the newly-added file
      selectedElm = ContentEdit.Root.get().focused();
      n = new ContentEdit.Text('p', {}, '');
      selectedElm.parent().attach(n, selectedElm.parent().children.indexOf(selectedElm) + 1);
      n.focus();
    }
  
  }, false);
  
}











function addHeroTool() {
  
  var heroDiv = document.getElementById('hero');
  var heroAdd = document.getElementById('hero-add');
  var formHero = document.getElementById('heroToUpload');
  
  tool.addEventListener('click', function(event) {
    
    // Show file dialog and upload the file to the server
    var input = document.getElementById('fileToUpload')
    input.click();
    input.onchange = function() {
      var data = new FormData();

      var files = input.files;
      for (var i = 0; i < files.length; i++) {
        var file = files[i];
        data.append('files', file, file.name);
      }
      data.append('page', window.location.pathname);
      
      var request = new XMLHttpRequest();
      request.open('POST', 'upload', true);
      request.onload = function () {
        if (request.status === 200) { // File uploaded
          response = JSON.parse(this.response);
          insertFile(response.filename, response.fileurl, response.extension);
        } else {
          //alert('An error occurred! Contact Andy Braren for help.');
        }
      };
      request.send(data);
      
    };
    
    function insertFile(filename, fileurl, extension) {
      html = '<p class="ce-element ce-element--type-text"><a href="' + fileurl + '" class="file-' + extension + '" data-filename="' + filename + '">' + filename + '</a></p>';
        
      // For whenever ContentTools is replaced
      // focus.insertAdjacentHTML('afterend', html);
      // var newelement = focus.nextSibling;
      // focus.parentNode.removeChild(focus);
      
      // ContentTools implementation
      // This is necessary to make the file's block editable after insertion
      // https://github.com/GetmeUK/ContentTools/issues/201
      selectedElm = ContentEdit.Root.get().focused();
      p = new ContentEdit.Text('p', {}, html);
      selectedElm.parent().attach(p, selectedElm.parent().children.indexOf(selectedElm) + 1);
      p.focus();
      
      // New paragraph below the newly-added file
      selectedElm = ContentEdit.Root.get().focused();
      n = new ContentEdit.Text('p', {}, '');
      selectedElm.parent().attach(n, selectedElm.parent().children.indexOf(selectedElm) + 1);
      n.focus();
    }
  
  }, false);
  
}



/* Trigger when Save button is clicked */
/* If empty, then the hidden class is confirmed or added */
/* If not empty, then the hidden class is removed */
function checkEmptyWidgets() {
  var widgets = document.getElementsByClassName('widget');
  
  for (var i = widgets.length - 1; i >= 0; i--) {
    var items = widgets[i].getElementsByClassName('items')[0];
    
    if (typeof items != 'undefined') {
            
      if (items.children.length > 0) { // If it has results, then remove the "hidden" class, otherwise make sure it's added
        widgets[i].classList.remove('hidden');
      } else {
        widgets[i].classList.add('hidden');
      }
      
    }
    
  }
  
}












turnOn = false;

function toggleItems() { // Enable dragula and toggle each item's href to data-href
  
  //dragula([document.getElementById('authors')]);
  //dragula([document.getElementById('groups')]);
  
  //if (window.location.pathname == '/settings') {
    //dragula([document.getElementsByClassName('menu')[0]]);
    //([document.getElementsByClassName('menu')[1]]);
    //dragula([document.getElementsByClassName('menu')[2]]);
  //}
  
  // dragula([document.getElementsByClassName('subnodes')[0]]);
    
  /*
  var elem = document.querySelectorAll('.menu')[1];
  var pckry = new Packery( elem, {
    itemSelector: 'li',
    gutter: 10
  });
  */
  
  // Toggles the value of turnOn between true (initial) to false
  turnOn = !turnOn;
      
  // Disable menu item hrefs and make them open the navitem modal instead 
  var navItems = document.querySelectorAll('.menu a');
  for (var i = 0; i < navItems.length; i++) {
    if (turnOn) {
      
      if (navItems[i].getAttribute('href')) {
        navItems[i].setAttribute('data-href', navItems[i].href);
        navItems[i].removeAttribute('href');
      }
      navItems[i].setAttribute('data-modal', 'navitem');
      
    } else {
      
      if (navItems[i].getAttribute('data-href') != '') {
        navItems[i].setAttribute('href', navItems[i].getAttribute('data-href'));
        navItems[i].removeAttribute('data-href');
      }
      navItems[i].removeAttribute('data-modal');
      
    }
  }
  
  // Enable the new modal buttons within the menu items
  if (turnOn) {
    enableModalButtons();
  } else {
    
  }
  
  var items = document.getElementsByClassName('item');
  for (var i = 0; i < items.length; i++) {
    if (items[i].hasAttribute('data-href')) {
      items[i].setAttribute('href', items[i].getAttribute('data-href'));
      items[i].removeAttribute('data-href');
    } else {
      items[i].setAttribute('data-href', items[i].href);
      items[i].removeAttribute('href');
    }
  }
  
}

function itemDeleteButtons() {
  var deleteButtons = document.getElementsByClassName('item-delete');
  for (var i = 0; i < deleteButtons.length; i++) {
    
    function deleteItem(event) {
      var target = getEventTarget(event);
      target.parentNode.parentNode.removeChild(target.parentNode);
    };
    
    deleteButtons[i].addEventListener('click', deleteItem, false);
    
  }
}


function itemConfirmButtons() {
  var confirmButtons = document.getElementsByClassName('item-confirm');
  for (var i = 0; i < confirmButtons.length; i++) {
    
    function confirmItem(event) {
      var target = getEventTarget(event);
      
      target.parentNode.parentNode.previousElementSibling.appendChild(target.parentNode);
      target.parentNode.removeChild(target);
    };
    
    confirmButtons[i].addEventListener('click', confirmItem, false);
    
  }
}






// Send the update content to the server to be saved
function onStateChange(ev) {
  if (ev.target.readyState == 4) {
    if (ev.target.status == '200') {
      try {
          // Andy - redirect to the destination URL that the server responded with
          // var data = JSON.parse(this.response);
          data = JSON.parse(this.response);
          //console.log(data.redirecturl);
          var redirecturl = data.redirecturl;
          var changeurl   = data.changeurl;
          //wait(3000);
          var command   = data.command;
          
          if (redirecturl) {
            window.location.href = redirecturl;
          }
          if (changeurl) {
            history.replaceState(null, null, changeurl);
            document.title = document.querySelector('h1').innerText;
          }
          if (command == 'goback') {
            window.history.back();
          }
          
      } catch (e) {
        
      }
    }
  }
};

/* Request to join a page/group */
var buttonjoin = document.getElementById('button-join');
if (buttonjoin != null) {
  buttonjoin.addEventListener('click', function() {
    
    data = new FormData();
    data.append('page', window.location.pathname);
    data.append('join', document.getElementById('datausername').getAttribute('data-slug'));
    
    var request = new XMLHttpRequest();
    request.open('POST', 'save', true);
    request.onload = function() {
      if (request.status >= 200 && request.status < 400) { // Success!
        buttonjoin.innerHTML = 'Request sent';
        buttonjoin.id = 'blah';
      } else {
        console.log("The server was reached, but returned an error");
      }
    };
    
    request.send(data);
    
  });
}

function savePage() {
  
  data = new FormData();
  
  /* Authors */
  var users = document.getElementById('authors');
  if (users != null) {
    var users = Array.prototype.slice.call(users.children);
    var arr = [];
    for (var i = users.length - 1; i >= 0; i--) {
      arr.push(users[i].getAttribute('data-username'));
    }
    var users = arr.reverse().join(', ');
    data.append('authors', users);
  }
  
  /* Requests */
  var requests = document.getElementById('requests');
  if (requests != null) {
    var requests = Array.prototype.slice.call(requests.children);
    var arr = [];
    for (var i = requests.length - 1; i >= 0; i--) {
      arr.push(requests[i].getAttribute('data-slug'));
    }
    var requests = arr.reverse().join(', ');
    data.append('requests', requests);
  }
  
  /* Projects */
  var projects = document.getElementById('projects');
  if (projects != null) {
    var projects = Array.prototype.slice.call(projects.children);
    var arr = [];
    for (var i = projects.length - 1; i >= 0; i--) {
      arr.push(projects[i].getAttribute('data-slug'));
    }
    var projects = arr.reverse().join(', ');
    data.append('projects', projects);
  }
  
  /* Groups */
  var groups = document.getElementById('groups');
  if (groups != null) {
    var groups = Array.prototype.slice.call(groups.children);
    var arr = [];
    for (var i = groups.length - 1; i >= 0; i--) {
      arr.push(groups[i].getAttribute('data-slug'));
    }
    var groups = arr.reverse().join(', ');
    data.append('groups', groups);
  }
  
  /* Events */
  var events = document.getElementById('events');
  if (events != null) {
    var events = Array.prototype.slice.call(events.children);
    var arr = [];
    for (var i = events.length - 1; i >= 0; i--) {
      arr.push(events[i].getAttribute('data-slug'));
    }
    var events = arr.reverse().join(', ');
    data.append('events', events);
  }
  
  /* Visibility */
  var visibility = document.getElementById('visibility');
  if (visibility != null) {
    data.append('visibility', visibility.options[visibility.selectedIndex].value);
  }
  
  /* Color */
  var color = document.getElementById('color');
  if (color != null) {
    data.append('color', color.options[color.selectedIndex].value);
  }
  
  /* Comments */
  var comments = document.getElementById('setting-comments');
  if (comments != null) {
    data.append('comments', comments.options[comments.selectedIndex].value);
  }
  
  /* Submissions */
  var submissions = document.getElementById('setting-submissions');
  if (submissions != null) {
    data.append('submissions', submissions.options[submissions.selectedIndex].value);
  }
  
  /* Title */
  var title = document.querySelectorAll("[data-name='title']")[0];
  if (title != null) {
    data.append('title', toMarkdown(title.innerHTML, { converters: kirbytagtweaks }));    
  }
  
  /* Text */
  var text = document.querySelectorAll("[data-name='text']")[0];
  if (text != null) {
    data.append('text', toMarkdown(text.innerHTML, { converters: kirbytagtweaks }));      
  }
  
  var modals = document.querySelector('#modals').children;
  if (modals) {
    for (var i = 0; i < modals.length; i++) {
      if (modals[i].classList.contains('visible')) {
        closeModal(modals[i]);
      }
    }
  }
  
  /* Navigation */
  if (window.location.pathname == '/settings') {
    var nav = document.querySelectorAll('.menu')[0];
    if (nav) {
      
      var arr = [];
      var items = nav.querySelectorAll('li');
      
      var items = nav.children;
      
      for (var i = 0; i < items.length; i++) {
        if (items[i].tagName == 'LI') {
          
          var element = items[i].querySelector('a');
          var subitemdiv = items[i].querySelector('.subnodes');
          var obj = {};
          
          obj['title'] = element.innerText;
          obj['uid'] = element.href.split('/').pop();
          
          if (element.nextElementSibling && element.nextElementSibling.classList.contains('subtitle')) {
            obj['subtitle'] = element.nextElementSibling.innerText;
          }
          
          if (subitemdiv) {
            var subitems = subitemdiv.querySelectorAll('li');
            var subarray = [];
            for (var e = 0; e < subitems.length; e++) {
              var subobj = {};
              subobj['title'] = subitems[e].querySelector('a').textContent;
              subobj['uid']   = subitems[e].querySelector('a').href.split('/').pop();
              subarray.push(subobj);
            }
            obj['sub'] = subarray;
          }
          
          arr.push(obj);
          
        }
      }
      data.append('menuprimary', JSON.stringify(arr));
    }
  }
  
  data.append('page', window.location.pathname);
    
  var request = new XMLHttpRequest();
  request.addEventListener('readystatechange', onStateChange);
  request.open('POST', 'save', true);
  request.send(data);
  
}






/* Save comment button */
var commentbutton = document.getElementById('save-comment');
if (commentbutton != null) {
  commentbutton.addEventListener('click', function(event) {
    saveComment();
  });
}

function saveComment() {
  
  /* Comment text */
  var comment = document.getElementById('add-comment');
  if (comment != null) {
    commentText = comment.getElementsByClassName('text')[0];
    if (commentText.childNodes[1].innerHTML != '') {
      
      data = new FormData();
      data.append('page', window.location.pathname);
      data.append('text', toMarkdown(commentText.innerHTML, { converters: kirbytagtweaks }));
      
      var request = new XMLHttpRequest();
      request.open('POST', 'saveblah', true);
      
      request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
          
          data = JSON.parse(this.response);
          
          // Clone the node, remove some parts, and then add it to the flow
          var clone = comment.cloneNode(true);
          clone.getElementsByClassName('text')[0].removeAttribute('contenteditable');
          clone.getElementsByClassName('comment-date')[0].innerHTML = 'just now';
          clone.getElementsByClassName('comment-date')[0].href = window.location + '/#' + data.id;
          clone.getElementsByClassName('post')[0].remove();
          clone.setAttribute('id', data.id);
          clone.setAttribute('data-id', data.dataid);
          comment.previousElementSibling.insertAdjacentElement('afterend', clone);
          
          // if the comment id is not immediately after the preceding comment, then there must
          // be other comments between the two. A "load comments" button between them should be
          // added in that case with a link to refresh the page, or ideally, pull down specific comment
          // html from the server and insert them into the flow
          
          // Reset the comment authoring area
          commentText.innerHTML = '<p placeholder="Add text here"></p>';
          
        } else {
          // error occured
        }
      };
      
      request.send(data);
    }
  }
  
}

// Maybe another function could set a 1-minute timer that refreshed timestamps if needed
// This would only be needed for "2 days ago" or whatever, not for set page dates
// This JS function should only be active while the page is in use to save CPU








/* Add author widget
*/
var authorwidget = document.getElementById('authors');
var authorfield = document.getElementById('author-add');
var authorresults = document.getElementById('author-results');
if (authorfield != null) {
  
  function lengthCheck() {
    if (this.value.length >= 2) {
      authorSearch();
    } else {
      clearSearch(authorresults);
    }
  }
  
  authorfield.addEventListener('input', lengthCheck, false);
  
  function authorSearch() {
    var request = new XMLHttpRequest();
    request.open('POST', '/api?users=all&search=' + authorfield.value, true);
    
    request.onload = function() {
      if (request.status >= 200 && request.status < 400) { // Success!
        var data = JSON.parse(this.response);
        if (data.status == 'success') {
          
          // Remove old results
          clearSearch(authorresults);
          
          // Add new results
          for (var i = 0; i < Object.keys(data.data).length; i++){
            
            //console.log(arr[i]);
            //console.log(data.data[i]['firstname'] + ' ' + data.data[i]['lastname']);
            
            var result = document.createElement('li');
            result.setAttribute('data-slug', data.data[i]['username']);
            result.setAttribute('data-name', data.data[i]['firstname'] + ' ' + data.data[i]['lastname']);
            result.setAttribute('data-avatar', data.data[i]['avatarURL']);
            result.setAttribute('data-major', data.data[i]['major']);
            result.setAttribute('data-profileURL', data.data[i]['profileURL']);
            result.setAttribute('data-color', data.data[i]['color']);
            
            result.innerHTML = data.data[i]['firstname'] + ' ' + data.data[i]['lastname'];
            
            authorresults.appendChild(result);
            result.addEventListener('click', selectResult, false);
          }
          
        } else if (data.status == 'error') { // A user was not found, meaning it's available
          console.log('Users not found');
        }
      } else {
        console.log("The server was reached, but returned an error");
      }
    };
    
    request.onerror = function() {
      console.log("Connection error");
    };
    
    request.send();
  }
  
  function clearSearch(field) { // Remove old results
    while (field.hasChildNodes()) {   
      field.removeChild(field.firstChild);
    }
  }
  
  function selectResult() {
    
    // Mark the parent widget as changed, preventing it from hiding itself
    event.target.closest(".widget").setAttribute('data-editor', "changed");
    
    clearSearch(authorresults);
    authorfield.value = '';
    authorfield.classList.remove('clicked');
    
    if (this.getAttribute('data-major') != 'undefined') {
      var major = '<span>' + this.getAttribute('data-major') + '</span>';
    } else {
      var major = '';
    }
    var resultHTML = '<a data-username="' + this.getAttribute('data-slug') + '" data-href="' + this.getAttribute('data-profileURL') + '" ><div class="item-delete"></div><div class="row"><img src="' + this.getAttribute('data-avatar') + '" class="' + this.getAttribute('data-color') + '" width="40" height="40"><div class="column"><span>' + this.getAttribute('data-name') + '</span>' + major + '</div></div></a>';
    authorwidget.insertAdjacentHTML('beforeend', resultHTML);
    itemDeleteButtons();
    
  }
  
}

/* Add group widget */
var groupwidget = document.getElementById('groups');
var groupfield = document.getElementById('group-add');
var groupresults = document.getElementById('group-results');
if (groupfield != null) {
    
  groupfield.addEventListener('input', lengthCheck, false);
  
  function lengthCheck() {
    if (this.value.length >= 2) {
      groupSearch();
    } else {
      clearSearch(groupresults);
    }
  }
  
  function groupSearch() {
    var request = new XMLHttpRequest();
    request.open('POST', '/api?groups=all&search=' + groupfield.value, true);
    
    request.onload = function() {
      if (request.status >= 200 && request.status < 400) { // Success!
        var data = JSON.parse(this.response);
        if (data.status == 'success') {
          
          // Remove old results
          clearSearch(groupresults);
          
          // Add new results
          for (var i = 0; i < Object.keys(data.data).length; i++){
            
            var result = document.createElement('li');
            result.setAttribute('data-title', data.data[i]['title']);
            result.setAttribute('data-groupslug', data.data[i]['groupslug']);
            result.setAttribute('data-groupurl', data.data[i]['groupURL']);
            result.setAttribute('data-logo', data.data[i]['logoURL']);
            result.setAttribute('data-color', data.data[i]['color']);
            
            result.innerHTML = data.data[i]['title'];
            
            groupresults.appendChild(result);
            result.addEventListener('click', selectResult, false);
          }
          
        } else if (data.status == 'error') { // A user was not found, meaning it's available
          console.log('Groups not found');
        }
      } else {
        console.log("The server was reached, but returned an error");
      }
    };
    
    request.onerror = function() {
      console.log("Connection error");
    };
    
    request.send();
  }
  
  function clearSearch(field) { // Remove old results
    while (field.hasChildNodes()) {   
      field.removeChild(field.firstChild);
    }
  }
  
  function selectResult() {
    clearSearch(groupresults);
    
    groupfield.value = '';
    groupfield.classList.remove('clicked');
    
    var resultHTML = '<a data-href="' + this.getAttribute('data-groupURL') + '" data-slug="' + this.getAttribute('data-groupslug') + '"><div class="item-delete"></div><div class="row"><img src="' + this.getAttribute('data-logo') + '" class="' + this.getAttribute('data-color') + '" width="40" height="40"><div class="column"><span>' + this.getAttribute('data-title') + '</span></div></div></a>';
    groupwidget.insertAdjacentHTML('beforeend', resultHTML);
    itemDeleteButtons();
  }
  
}








/* Widget searches */
//var searchwidgets = document.querySelectorAll('[data-role="search"]');
var searchforms = document.querySelectorAll('[data-role="search"]');
if (searchforms != null) {
  for (var i = 0; i < searchforms.length; i++) {
    
    var searchinput = searchforms[i].getElementsByTagName('INPUT')[0];
    searchinput.addEventListener('input', lengthCheck, false);
    
    function lengthCheck() {
      var type = this.parentNode.parentNode.parentNode.nextElementSibling.id;
      var resultnode = this.nextElementSibling;
      var input = this;
      var query = this.value;
      
      if (this.value.length >= 2) {
        doSearch(type, query, input, resultnode);
      } else {
        clearSearch(resultnode);
      }
    }
    
    function clearSearch(resultnode) { // Remove old results
      while (resultnode.hasChildNodes()) {   
        resultnode.removeChild(resultnode.firstChild);
      }
    }
    
    function doSearch(type, query, input, resultnode) {
      var request = new XMLHttpRequest();
      request.open('POST', '/api?' + type + '=all&search=' + query, true);
      
      request.onload = function() {
        if (request.status >= 200 && request.status < 400) { // Success!
          var data = JSON.parse(this.response);
          if (data.status == 'success') {
            
            // Remove old results
            clearSearch(resultnode);
            
            // Add new results
            for (var i = 0; i < Object.keys(data.data).length; i++){
              
              var result = document.createElement('li');
              result.setAttribute('data-title', data.data[i]['title']);
              result.setAttribute('data-slug',  data.data[i]['slug']);
              result.setAttribute('data-url',   data.data[i]['url']);
              result.setAttribute('data-image', data.data[i]['image']);
              result.setAttribute('data-color', data.data[i]['color']);
              
              result.innerHTML = data.data[i]['title'];
              
              if (type == 'users') {
                result.setAttribute('data-slug', data.data[i]['username']);
                result.setAttribute('data-name', data.data[i]['firstname'] + ' ' + data.data[i]['lastname']);
                result.setAttribute('data-avatar', data.data[i]['avatarURL']);
                result.setAttribute('data-major', data.data[i]['major']);
                result.setAttribute('data-profileURL', data.data[i]['profileURL']);
                result.innerHTML = data.data[i]['firstname'] + ' ' + data.data[i]['lastname'];
              }
              
              if (type == 'events') {
                result.setAttribute('data-date', data.data[i]['date']);
              }
              
              resultnode.appendChild(result);
              //result.addEventListener('click', selectResult, false);
              result.addEventListener('click', function() {
                clearSearch(resultnode);
                
                input.value = '';
                input.classList.remove('clicked');
                
                var title = this.getAttribute('data-title');
                var slug = this.getAttribute('data-slug');
                var url  = this.getAttribute('data-url');
                
                var color = this.getAttribute('data-color');
                
                if (this.getAttribute('data-image') !== 'undefined') {
                  var image = '<img src="' + this.getAttribute('data-image') + '" class="' + color + '" width="40" height="40">';
                } else {
                  var image = '';
                }
                
                var date = (this.getAttribute('data-date') != null) ? '<span>' + this.getAttribute('data-date') + '</span>': '';
                
                var resultHTML = '<a class="item" data-slug="' + slug + '" data-href="' + url + '"><div class="item-delete"></div><div class="row">' + image + '<div class="column"><span>' + title + '</span>' + date + '</div></div></a>';
                
                if (type == 'users') {
                  var username = this.getAttribute('data-slug');
                  var name  = this.getAttribute('data-name');
                  var image  = this.getAttribute('data-avatar');
                  var major = '<span>' + this.getAttribute('data-major') + '</span>';
                    if (this.getAttribute('data-major') == 'undefined') {
                      var major = '';
                    }
                  var url = this.getAttribute('data-profileURL');
                  var resultHTML = '<a class="item" data-slug="' + username + '" data-href="' + url + '" ><div class="item-delete"></div><div class="row"><img src="' + image + '" class="' + color + '" width="40" height="40"><div class="column"><span>' + name + '</span>' + major + '</div></div></a>';
                }
                
                input.parentNode.parentNode.parentNode.nextElementSibling.insertAdjacentHTML('beforeend', resultHTML);
                itemDeleteButtons();
              }, false);
            }
            
          } else if (data.status == 'error') {
            console.log('No results found');
          }
        } else {
          console.log('The server was reached, but returned an error');
        }
      };
      
      request.onerror = function() {
        console.log('Connection error');
      };
      
      request.send();
    }
  }
}



















/* Add a class to input elements after they've been clicked */
//var inputs = document.getElementsByTagName('input');
var inputs = document.querySelectorAll('input,select');
for (var i = 0; i < inputs.length; i++) {
  
  function addClicked(event) {
    var target = getEventTarget(event);
    if (target.value != '') {
      target.classList.add('clicked');
      target.classList.add('hasbeenclicked');
    }
    if (target.value == '') {
      target.classList.remove('clicked');
    }
  };
  
  function removeClicked(event) {
    var target = getEventTarget(event);
    target.classList.remove('clicked');
  };
  
  inputs[i].addEventListener('focus', addClicked, false);
  
  // Add class after unfocusing, unless the box is blank
  inputs[i].addEventListener('focusout', addClicked, false);
  inputs[i].addEventListener('blur', addClicked, false); // Because Firefox doesn't support focusout https://developer.mozilla.org/en-US/docs/Web/Events/blur
  //inputs[i].addEventListener('focus', removeClicked, false);
  
}

function getEventTarget(e) {
  e = e || window.event;
  return e.target || e.srcElement;
}




// Better Video Embeds
// http://www.sitepoint.com/faster-youtube-embeds-javascript/

function videoEmbed(){
  
  // select the parent element that holds the image and iframe
  var videos = document.getElementsByClassName("video-container");
  // run through every matching element to set the onclick property
  for (var i=0; i<videos.length; i++) {
    
    // when an element is clicked, get its iframe and change its attributes to make it load and display
    videos[i].onclick = function() {
      
      var iframeEl = this.getElementsByTagName('iframe')[0];
      
      if (iframeEl.getAttribute('data-src')) {
        iframeEl.setAttribute('src',iframeEl.getAttribute('data-src'));
        iframeEl.removeAttribute('data-src'); //use only if you need to remove data-src attribute after setting src
      }
      
      iframeEl.classList.add('visible');
      
      iframeEl.click();
      
    }
  }
};




/* Hero Image Switcher */

function heroImages() {
  if (document.contains(document.getElementById('hero')) && document.getElementById('hero').classList.contains('carousel')) {
    var imgs = document.getElementById('hero').getElementsByTagName('figure'),
      index = 0;
    setInterval(function () {
      imgs[index].style.opacity = '0';
      index = (index + 1) % imgs.length;
      imgs[index].style.opacity = '1';
    }, 7000);
  }
};







/* Content Filtering
 * When a <select> within #filters is clicked, add its value to the data-filters attribute of <main> so that CSS can hide items
*/
var main = document.getElementsByTagName('main')[0];
var filters = document.getElementById('filters');
if (filters != null) {
  
  // Tufts Affiliation, Department, Major
  var selects = filters.getElementsByTagName('select');
  for (var i=0; i<selects.length; i++) {
    selects[i].addEventListener('click', function(event) {
      var initialvalue = getEventTarget(event).value;
      this.onchange = function() {
        main.removeAttribute('data-filters', initialvalue);
        //main.setAttribute('data-filters', main.getAttribute('data-filters') + ' ' + this.value);
        main.setAttribute('data-filters', this.value);
        //main.classList.remove(initialvalue);
        //main.classList.add(this.value);
      }
    }, false);
  }
  
  // Class year
  var inputs = filters.getElementsByTagName('input');
  for (var i=0; i<inputs.length; i++) {
    inputs[i].addEventListener('input', function(event) {
      
      var initialvalue = getEventTarget(event).value;
      main.removeAttribute('data-filters', initialvalue);
      
      var css = document.createElement("style");
      css.type = "text/css";
      css.innerHTML = 'main[data-filters="' + this.value + '"] article .cards-makers a:not([data-filters~="' + this.value + '"]) { display: none; }"';
      
      if (this.checkValidity()) {
        main.setAttribute('data-filters', this.value);
        

        document.body.appendChild(css);
      }
    }, false);
  }
  
}






/* Update username on signup page and check for 
  - http://stackoverflow.com/questions/574941/best-way-to-track-onchange-as-you-type-in-input-type-text/26202266#26202266
  - https://developer.mozilla.org/en-US/docs/Web/API/GlobalEventHandlers/oninput
  - AJAX GET: http://youmightnotneedjquery.com/
*/
var usernamefield = document.getElementById('usernamefield');
var usernameurl = document.getElementById('usernameurl');
if (usernamefield != null) {
  
  var usernameurl = document.getElementById('usernameurl');
  usernamefield.addEventListener('input', function() {    
    usernameurl.innerText = usernamefield.value;
    usernameurl.style.color = '';
    document.getElementById('usernamelabel').style.color = '';
    document.getElementById('usernamemessage').innerText = '';
  }, false);
  
  usernamefield.addEventListener('focusout', queryUsername, false);
  usernamefield.addEventListener('blur', queryUsername, false); // For Firefox
  
}

/* Do the same for the new site field */
var newsitefield = document.getElementById('newsitefield');
if (newsitefield != null) {
  
  var newsitesubdomain = document.getElementById('newsitesubdomain');
  newsitefield.addEventListener('input', function() {    
    newsitesubdomain.innerText = newsitefield.value;
    newsitesubdomain.style.color = '';
    document.getElementById('newsitelabel').style.color = '';
    document.getElementById('newsitemessage').innerText = '';
  }, false);
  
  //newsitefield.addEventListener('focusout', queryUsername, false);
  //newsitefield.addEventListener('blur', queryUsername, false); // For Firefox
  
}

// Create new demo site
function newSite() {
  data = new FormData();
  data.append('desiredname', document.querySelector('#newsitefield').value);
  var request = new XMLHttpRequest();
  request.addEventListener('readystatechange', onStateChange);
  request.open('POST', 'newsite', true);
  request.send(data);
}
var newsiteButtons = document.querySelectorAll('[data-action="newsite"]');
if (newsiteButtons) {
  for (var i = 0; i < newsiteButtons.length; i++) {
    newsiteButtons[i].addEventListener('click', function() {
      newSite();
    }, false);
  }
}





function queryUsername() {
  
  var request = new XMLHttpRequest();
  request.open('POST', 'api?users=' + usernamefield.value, true);
  request.onload = function() {
    if (request.status >= 200 && request.status < 400) { // Success!
      var data = JSON.parse(request.responseText);
      if (data.status == 'success') { // A user was found, which means the username is occupied
        console.log('Username is not available');
        document.getElementById('usernameurl').style.color = 'crimson';
        document.getElementById('usernamelabel').style.color = 'crimson';
        document.getElementById('usernamemessage').innerText = ' (taken)';
      } else if (data.status == 'error') { // A user was not found, meaning it's available
        console.log('Username is available');
        document.getElementById('usernameurl').style.color = 'green';
        document.getElementById('usernamelabel').style.color = '';
      }
    } else {
      console.log("The server was reached, but returned an error");
    }
  };
  
  request.onerror = function() {
    console.log("Connection error");
  };
  
  request.send();
}


// Set the default coordinates of the toolbox to be directly right of the text box
function positionToolbox() {
  var rect = document.getElementsByClassName('text')[0].getBoundingClientRect();
  var coordinates = Math.round(rect.right + 30) + ',' + Math.round(rect.top);
  localStorage.setItem('ct-toolbox-position', coordinates);
}

// Automatically open the editor immediately if the page is a new page
function checkIfNewPage() {
  var slug = window.location.href.split('/').pop();
  if (slug && !slug.match(/[a-z]/i)) {
    
    history.replaceState({}, document.title, window.location.href.split('#')[0]);
    
  }
}

window.addEventListener('load', function(event) {
  positionToolbox();
  checkIfNewPage();
});


// Theme Checker
document.addEventListener('DOMContentLoaded', function(event) {
  if (localStorage.getItem('theme') != null) {
    document.body.classList.add(localStorage.getItem('theme'));
  }
});






//setCookie();
function setCookie() {
  var now = new Date();
  now.setTime(now.getTime() + 24 * 3600 * 1000);  
  document.cookie = "newcookie=" + 'hi' + ";path=/;max-age=" + (24*3600) + ";expires=" + now.toUTCString() + ";";
}

function getQueryString(field) {
  var href = window.location.href;
  var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
  var string = reg.exec(href);
  return string ? string[1] : null;
}

function getCookie(name) {
  var cookie = document.cookie;
  var prefix = name + "=";
  var begin = cookie.indexOf("; " + prefix);
  if (begin == -1) {
    begin = cookie.indexOf(prefix);
    if (begin != 0) return null;
  } else {
    begin += 2;
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
    end = cookie.length;
    }
  }
  return decodeURI(cookie.substring(begin + prefix.length, end));
}

// Set the cookie
var affiliateID = getQueryString("affiliate");
if (affiliateID != null) {
  var now = new Date();
  now.setTime(now.getTime() + 24 * 3600 * 1000);  
  document.cookie = "affiliateID=" + affiliateID + ";path=/;max-age=" + (24*3600) + ";expires=" + now.toUTCString() + ";";
}

// Check the cookie
var affiliateField = document.getElementById('BillingNewAddress_AffiliateID');
if (affiliateField != null) {
  var affiliateCookie = getCookie("affiliateID");
  if (affiliateCookie != null) {
    affiliateField.value = affiliateCookie;
  }
}








/* Progress Nav
  - http://lab.hakim.se/progress-nav/
-------------------------------------------------- */
function progressNav() {

	var toc = document.querySelector( '.widget.toc .toc-items' );
	var tocPath = document.querySelector( '.toc-marker path' );
	var tocItems;
	
	if (toc != null) {
  	// Factor of screen size that the element must cross
  	// before it's considered visible
  	var TOP_MARGIN = 0.1,
  		BOTTOM_MARGIN = 0.2;
  
  	var pathLength;
  
  	window.addEventListener( 'resize', drawPath, false );
  	window.addEventListener( 'scroll', sync, false );
  
  	drawPath();
  
  	function drawPath() {
  
  		tocItems = [].slice.call( toc.querySelectorAll( 'li' ) );
  
  		// Cache element references and measurements
  		tocItems = tocItems.map( function( item ) {
  			var anchor = item.querySelector( 'a' );
  			var target = document.getElementById( anchor.getAttribute( 'href' ).slice( 1 ) );
  
  			return {
  				listItem: item,
  				anchor: anchor,
  				target: target
  			};
  		} );
  
  		// Remove missing targets
  		tocItems = tocItems.filter( function( item ) {
  			return !!item.target;
  		} );
  
  		var path = [];
  		var pathIndent;
  
  		tocItems.forEach( function( item, i ) {
  
  			var x = item.anchor.offsetLeft - 5,
  				y = item.anchor.offsetTop,
  				height = item.anchor.offsetHeight;
  
  			if( i === 0 ) {
  				path.push( 'M', x, y, 'L', x, y + height );
  				item.pathStart = 0;
  			}
  			else {
  				// Draw an additional line when there's a change in
  				// indent levels
  				if( pathIndent !== x ) path.push( 'L', pathIndent, y );
  
  				path.push( 'L', x, y );
  
  				// Set the current path so that we can measure it
  				tocPath.setAttribute( 'd', path.join( ' ' ) );
  				item.pathStart = tocPath.getTotalLength() || 0;
  
  				path.push( 'L', x, y + height );
  			}
  
  			pathIndent = x;
  
  			tocPath.setAttribute( 'd', path.join( ' ' ) );
  			item.pathEnd = tocPath.getTotalLength();
  
  		} );
  
  		pathLength = tocPath.getTotalLength();
  
  		sync();
  
  	}
  
  	function sync() {
  
  		var windowHeight = window.innerHeight;
  
  		var pathStart = pathLength,
  			pathEnd = 0;
  
  		var visibleItems = 0;
  
  		tocItems.forEach( function( item ) {
  
  			var targetBounds = item.target.getBoundingClientRect();
  
  			if( targetBounds.bottom > windowHeight * TOP_MARGIN && targetBounds.top < windowHeight * ( 1 - BOTTOM_MARGIN ) ) {
  				pathStart = Math.min( item.pathStart, pathStart );
  				pathEnd = Math.max( item.pathEnd, pathEnd );
  
  				visibleItems += 1;
  
  				item.listItem.classList.add( 'visible' );
  			}
  			else {
  				item.listItem.classList.remove( 'visible' );
  			}
  
  		} );
  
  		// Specify the visible path or hide the path altogether
  		// if there are no visible items
  		if( visibleItems > 0 && pathStart < pathEnd ) {
  			tocPath.setAttribute( 'stroke-dashoffset', '1' );
  			tocPath.setAttribute( 'stroke-dasharray', '1, '+ pathStart +', '+ ( pathEnd - pathStart ) +', ' + pathLength );
  			tocPath.setAttribute( 'opacity', 1 );
  		}
  		else {
  			//tocPath.setAttribute( 'opacity', 0 );
  		}
  
  	}
	}

};

/* https://stripe.com/docs/stripe-js/elements/payment-request-button
*/

function checkStripe() {
  
  var stripeButton = document.getElementById('payment-request-button');
  if (stripeButton != null) {
    
    var stripeAmount = parseInt(stripeButton.getAttribute('data-amount'));
    var stripeKey = stripeButton.getAttribute('data-pkey');
    
    var stripe = Stripe(stripeKey);
    
    var paymentRequest = stripe.paymentRequest({
      country: 'US',
      currency: 'usd',
      total: {
        label: 'Demo total',
        amount: stripeAmount,
      },
      requestShipping: true,
      shippingOptions: [{
        id: 'free-shipping',
        label: 'Free shipping',
        detail: 'Arrives in 5 to 7 days',
        amount: 0,
      }],
    });
    
    var elements = stripe.elements();
    var prButton = elements.create('paymentRequestButton', {
      paymentRequest: paymentRequest,
      style: {
        paymentRequestButton: {
          type: 'default',
          theme: 'dark',
          height: '40px',
        },
      },
    });
    
    // Check the availability of the Payment Request API first.
    paymentRequest.canMakePayment().then(function(result) {
      if (result) {
        prButton.mount('#payment-request-button');
      } else {
        document.getElementById('payment-request-button').style.display = 'none';
      }
    });
    
    paymentRequest.on('token', function(ev) {
      // Send the token to your server to charge it!
      fetch('/stripecharge', {
        method: 'POST',
        body: JSON.stringify({token: ev.token.id, page: window.location.href}),
      })
      .then(function(response) {
        if (response.ok) {
          // Report to the browser that the payment was successful, prompting
          // it to close the browser payment interface.
          ev.complete('success');
        } else {
          // Report to the browser that the payment failed, prompting it to
          // re-show the payment interface, or show an error message and close
          // the payment interface.
          ev.complete('fail');
        }
      });
    });
    
  }
}








var comments = document.getElementsByClassName('comments')[0];
disqusLoaded = false;

function loadDisqus() {
  
  var disqus_shortname = 'tinkertry';
  
  var disqus_config = function () {
    
    this.page.url = window.location.href.split('#')[0];
    this.page.identifier = window.location.pathname.substring(1);
  };
  
  var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
  dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
  (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  disqusLoaded = true;
}

function findTop(obj) {
  var curtop = 0;
  if (obj.offsetParent) {
    do {
      curtop += obj.offsetTop;
    } while (obj = obj.offsetParent);
    return curtop;
  }
}

if (window.location.hash.indexOf('#comment-1913694120') > 0) {
  loadDisqus();
}

if (window.location.hash) {
  if (window.location.hash.indexOf('#comment') != -1) {
    loadDisqus();
  }
}

if(comments) {
  var commentsOffset = findTop(comments);
  window.onscroll = function() {
    if(!disqusLoaded && window.pageYOffset > commentsOffset - 1500) {
      loadDisqus();
    }
  }
}





/*!
 * headroom.js v0.8.0 - Give your page some headroom. Hide your header until you need it
 * Copyright (c) 2016 Nick Williams - http://wicky.nillia.ms/headroom.js
 * License: MIT
 */
!function(a,b){"use strict";"function"==typeof define&&define.amd?define([],b):"object"==typeof exports?module.exports=b():a.Headroom=b()}(this,function(){"use strict";function a(a){this.callback=a,this.ticking=!1}function b(a){return a&&"undefined"!=typeof window&&(a===window||a.nodeType)}function c(a){if(arguments.length<=0)throw new Error("Missing arguments in extend function");var d,e,f=a||{};for(e=1;e<arguments.length;e++){var g=arguments[e]||{};for(d in g)"object"!=typeof f[d]||b(f[d])?f[d]=f[d]||g[d]:f[d]=c(f[d],g[d])}return f}function d(a){return a===Object(a)?a:{down:a,up:a}}function e(a,b){b=c(b,e.options),this.lastKnownScrollY=0,this.elem=a,this.tolerance=d(b.tolerance),this.classes=b.classes,this.offset=b.offset,this.scroller=b.scroller,this.initialised=!1,this.onPin=b.onPin,this.onUnpin=b.onUnpin,this.onTop=b.onTop,this.onNotTop=b.onNotTop,this.onBottom=b.onBottom,this.onNotBottom=b.onNotBottom}var f={bind:!!function(){}.bind,classList:"classList"in document.documentElement,rAF:!!(window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame)};return window.requestAnimationFrame=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame,a.prototype={constructor:a,update:function(){this.callback&&this.callback(),this.ticking=!1},requestTick:function(){this.ticking||(requestAnimationFrame(this.rafCallback||(this.rafCallback=this.update.bind(this))),this.ticking=!0)},handleEvent:function(){this.requestTick()}},e.prototype={constructor:e,init:function(){return e.cutsTheMustard?(this.debouncer=new a(this.update.bind(this)),this.elem.classList.add(this.classes.initial),setTimeout(this.attachEvent.bind(this),100),this):void 0},destroy:function(){var a=this.classes;this.initialised=!1,this.elem.classList.remove(a.unpinned,a.pinned,a.top,a.notTop,a.initial),this.scroller.removeEventListener("scroll",this.debouncer,!1)},attachEvent:function(){this.initialised||(this.lastKnownScrollY=this.getScrollY(),this.initialised=!0,this.scroller.addEventListener("scroll",this.debouncer,!1),this.debouncer.handleEvent())},unpin:function(){var a=this.elem.classList,b=this.classes;!a.contains(b.pinned)&&a.contains(b.unpinned)||(a.add(b.unpinned),a.remove(b.pinned),this.onUnpin&&this.onUnpin.call(this))},pin:function(){var a=this.elem.classList,b=this.classes;a.contains(b.unpinned)&&(a.remove(b.unpinned),a.add(b.pinned),this.onPin&&this.onPin.call(this))},top:function(){var a=this.elem.classList,b=this.classes;a.contains(b.top)||(a.add(b.top),a.remove(b.notTop),this.onTop&&this.onTop.call(this))},notTop:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notTop)||(a.add(b.notTop),a.remove(b.top),this.onNotTop&&this.onNotTop.call(this))},bottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.bottom)||(a.add(b.bottom),a.remove(b.notBottom),this.onBottom&&this.onBottom.call(this))},notBottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notBottom)||(a.add(b.notBottom),a.remove(b.bottom),this.onNotBottom&&this.onNotBottom.call(this))},getScrollY:function(){return void 0!==this.scroller.pageYOffset?this.scroller.pageYOffset:void 0!==this.scroller.scrollTop?this.scroller.scrollTop:(document.documentElement||document.body.parentNode||document.body).scrollTop},getViewportHeight:function(){return window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight},getElementPhysicalHeight:function(a){return Math.max(a.offsetHeight,a.clientHeight)},getScrollerPhysicalHeight:function(){return this.scroller===window||this.scroller===document.body?this.getViewportHeight():this.getElementPhysicalHeight(this.scroller)},getDocumentHeight:function(){var a=document.body,b=document.documentElement;return Math.max(a.scrollHeight,b.scrollHeight,a.offsetHeight,b.offsetHeight,a.clientHeight,b.clientHeight)},getElementHeight:function(a){return Math.max(a.scrollHeight,a.offsetHeight,a.clientHeight)},getScrollerHeight:function(){return this.scroller===window||this.scroller===document.body?this.getDocumentHeight():this.getElementHeight(this.scroller)},isOutOfBounds:function(a){var b=0>a,c=a+this.getScrollerPhysicalHeight()>this.getScrollerHeight();return b||c},toleranceExceeded:function(a,b){return Math.abs(a-this.lastKnownScrollY)>=this.tolerance[b]},shouldUnpin:function(a,b){var c=a>this.lastKnownScrollY,d=a>=this.offset;return c&&d&&b},shouldPin:function(a,b){var c=a<this.lastKnownScrollY,d=a<=this.offset;return c&&b||d},update:function(){var a=this.getScrollY(),b=a>this.lastKnownScrollY?"down":"up",c=this.toleranceExceeded(a,b);this.isOutOfBounds(a)||(a<=this.offset?this.top():this.notTop(),a+this.getViewportHeight()>=this.getScrollerHeight()?this.bottom():this.notBottom(),this.shouldUnpin(a,c)?this.unpin():this.shouldPin(a,c)&&this.pin(),this.lastKnownScrollY=a)}},e.options={tolerance:{up:5,down:0},offset:70,scroller:window,classes:{pinned:"headroom--pinned",unpinned:"headroom--unpinned",top:"headroom--top",notTop:"headroom--not-top",bottom:"headroom--bottom",notBottom:"headroom--not-bottom",initial:"headroom"}},e.cutsTheMustard="undefined"!=typeof f&&f.rAF&&f.bind&&f.classList,e});

/*!
  hey, [be]Lazy.js - v1.8.2 - 2016.10.25
  A fast, small and dependency free lazy load script (https://github.com/dinbror/blazy)
  (c) Bjoern Klinggaard - @bklinggaard - http://dinbror.dk/blazy
*/
  (function(q,m){"function"===typeof define&&define.amd?define(m):"object"===typeof exports?module.exports=m():q.Blazy=m()})(this,function(){function q(b){var c=b._util;c.elements=E(b.options);c.count=c.elements.length;c.destroyed&&(c.destroyed=!1,b.options.container&&l(b.options.container,function(a){n(a,"scroll",c.validateT)}),n(window,"resize",c.saveViewportOffsetT),n(window,"resize",c.validateT),n(window,"scroll",c.validateT));m(b)}function m(b){for(var c=b._util,a=0;a<c.count;a++){var d=c.elements[a],e;a:{var g=d;e=b.options;var p=g.getBoundingClientRect();if(e.container&&y&&(g=g.closest(e.containerClass))){g=g.getBoundingClientRect();e=r(g,f)?r(p,{top:g.top-e.offset,right:g.right+e.offset,bottom:g.bottom+e.offset,left:g.left-e.offset}):!1;break a}e=r(p,f)}if(e||t(d,b.options.successClass))b.load(d),c.elements.splice(a,1),c.count--,a--}0===c.count&&b.destroy()}function r(b,c){return b.right>=c.left&&b.bottom>=c.top&&b.left<=c.right&&b.top<=c.bottom}function z(b,c,a){if(!t(b,a.successClass)&&(c||a.loadInvisible||0<b.offsetWidth&&0<b.offsetHeight))if(c=b.getAttribute(u)||b.getAttribute(a.src)){c=c.split(a.separator);var d=c[A&&1<c.length?1:0],e=b.getAttribute(a.srcset),g="img"===b.nodeName.toLowerCase(),p=(c=b.parentNode)&&"picture"===c.nodeName.toLowerCase();if(g||void 0===b.src){var h=new Image,w=function(){a.error&&a.error(b,"invalid");v(b,a.errorClass);k(h,"error",w);k(h,"load",f)},f=function(){g?p||B(b,d,e):b.style.backgroundImage='url("'+d+'")';x(b,a);k(h,"load",f);k(h,"error",w)};p&&(h=b,l(c.getElementsByTagName("source"),function(b){var c=a.srcset,e=b.getAttribute(c);e&&(b.setAttribute("srcset",e),b.removeAttribute(c))}));n(h,"error",w);n(h,"load",f);B(h,d,e)}else b.src=d,x(b,a)}else"video"===b.nodeName.toLowerCase()?(l(b.getElementsByTagName("source"),function(b){var c=a.src,e=b.getAttribute(c);e&&(b.setAttribute("src",e),b.removeAttribute(c))}),b.load(),x(b,a)):(a.error&&a.error(b,"missing"),v(b,a.errorClass))}function x(b,c){v(b,c.successClass);c.success&&c.success(b);b.removeAttribute(c.src);b.removeAttribute(c.srcset);l(c.breakpoints,function(a){b.removeAttribute(a.src)})}function B(b,c,a){a&&b.setAttribute("srcset",a);b.src=c}function t(b,c){return-1!==(" "+b.className+" ").indexOf(" "+c+" ")}function v(b,c){t(b,c)||(b.className+=" "+c)}function E(b){var c=[];b=b.root.querySelectorAll(b.selector);for(var a=b.length;a--;c.unshift(b[a]));return c}function C(b){f.bottom=(window.innerHeight||document.documentElement.clientHeight)+b;f.right=(window.innerWidth||document.documentElement.clientWidth)+b}function n(b,c,a){b.attachEvent?b.attachEvent&&b.attachEvent("on"+c,a):b.addEventListener(c,a,{capture:!1,passive:!0})}function k(b,c,a){b.detachEvent?b.detachEvent&&b.detachEvent("on"+c,a):b.removeEventListener(c,a,{capture:!1,passive:!0})}function l(b,c){if(b&&c)for(var a=b.length,d=0;d<a&&!1!==c(b[d],d);d++);}function D(b,c,a){var d=0;return function(){var e=+new Date;e-d<c||(d=e,b.apply(a,arguments))}}var u,f,A,y;return function(b){if(!document.querySelectorAll){var c=document.createStyleSheet();document.querySelectorAll=function(a,b,d,h,f){f=document.all;b=[];a=a.replace(/\[for\b/gi,"[htmlFor").split(",");for(d=a.length;d--;){c.addRule(a[d],"k:v");for(h=f.length;h--;)f[h].currentStyle.k&&b.push(f[h]);c.removeRule(0)}return b}}var a=this,d=a._util={};d.elements=[];d.destroyed=!0;a.options=b||{};a.options.error=a.options.error||!1;a.options.offset=a.options.offset||100;a.options.root=a.options.root||document;a.options.success=a.options.success||!1;a.options.selector=a.options.selector||".b-lazy";a.options.separator=a.options.separator||"|";a.options.containerClass=a.options.container;a.options.container=a.options.containerClass?document.querySelectorAll(a.options.containerClass):!1;a.options.errorClass=a.options.errorClass||"b-error";a.options.breakpoints=a.options.breakpoints||!1;a.options.loadInvisible=a.options.loadInvisible||!1;a.options.successClass=a.options.successClass||"b-loaded";a.options.validateDelay=a.options.validateDelay||25;a.options.saveViewportOffsetDelay=a.options.saveViewportOffsetDelay||50;a.options.srcset=a.options.srcset||"data-srcset";a.options.src=u=a.options.src||"data-src";y=Element.prototype.closest;A=1<window.devicePixelRatio;f={};f.top=0-a.options.offset;f.left=0-a.options.offset;a.revalidate=function(){q(a)};a.load=function(a,b){var c=this.options;void 0===a.length?z(a,b,c):l(a,function(a){z(a,b,c)})};a.destroy=function(){var a=this._util;this.options.container&&l(this.options.container,function(b){k(b,"scroll",a.validateT)});k(window,"scroll",a.validateT);k(window,"resize",a.validateT);k(window,"resize",a.saveViewportOffsetT);a.count=0;a.elements.length=0;a.destroyed=!0};d.validateT=D(function(){m(a)},a.options.validateDelay,a);d.saveViewportOffsetT=D(function(){C(a.options.offset)},a.options.saveViewportOffsetDelay,a);C(a.options.offset);l(a.options.breakpoints,function(a){if(a.width>=window.screen.width)return u=a.src,!1});setTimeout(function(){q(a)})}});

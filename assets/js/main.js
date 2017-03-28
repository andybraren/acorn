/* Reopen login window if login fails */
/*
  Switched to adding class via PHP for now, but should look into better method
if (window.location.href.indexOf('login:failed') != -1) {
  document.getElementById('modal-login').classList.add('visible');
}
*/

window.onload = function() {
  checkTheme();
  checkFontSize();
  checkFontFamily();
  
  videoEmbed();
  heroImages();
  enableModalButtons();
  
  /* Activate okayNav */
  // var okaynav = new OkayNav('#navigation', '');
  
  /* Activate bLazy */
  var bLazy = new Blazy({ 
      // selector: 'img', // all images
      offset: 500
  });
  
  /* Activate stickyfill */
  var stickyElements = document.getElementsByClassName('sticky');
  for (var i = stickyElements.length - 1; i >= 0; i--) {
      Stickyfill.add(stickyElements[i]);
  }
  
  progressNav();
  
  /* Activate object-fit polyfill for IE/Edge */
  objectFitImages();
  
  /* Activate headroom */
  var myElement = document.querySelector("header"); // grab an element
  var headroom  = new Headroom(myElement); // construct an instance of Headroom, passing the element
  headroom.init(); // initialise
  
  /* Activate tooltips */
  var myTooltip = Frtooltip();
  myTooltip.init();

  
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
    editbutton.addEventListener('click', openEdit);
  }
  

  
  
}

clickcount = 0;
function openEdit() {
  
  var editbutton = document.getElementById('button-edit');
  
  /* Hero and icon image editing */
  var formupload = document.getElementById('upload-form');

  var iconimg = document.getElementById('icon');
  var formAvatar = document.getElementById('avatarToUpload');
  var formSettings = document.getElementById('form-settings');
  var formSettingsVisibility = document.getElementById('visibility');
  
  if(clickcount == 0) {
    document.body.classList.toggle('editing');
    replaceFigures(); // from editor.js, need to finish before ignition.edit
    editor.ignition().edit();
    addFileTool();
    toggleHero(clickcount, formupload);
    //toggleAuthors(clickcount);
    //toggleGroups(clickcount);
    toggleItems(clickcount);
    itemDeleteButtons();
    itemConfirmButtons();
    Blazy();
    editbutton.innerHTML = 'Save';
    clickcount = 1;
  } else {
    checkEmptyWidgets()
    document.body.classList.toggle('editing');
    toggleHero(clickcount, formupload);
    //toggleAuthors(clickcount);
    //toggleGroups(clickcount);
    toggleItems(clickcount);
    editbutton.innerHTML = 'Edit';
    editor.ignition().confirm();
    savePage()
    videoEmbed();
    clickcount = 0;
  }
  
  if (iconimg != null) {
    iconimg.addEventListener('click', function() {
      formAvatar.click();
    });
    formAvatar.onchange = function() {
      editor.ignition().confirm();
      formupload.submit();
    };
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
  TOC Toggle
  - Displays the TOC on mobile devices
--------------------------------------------------*/


var tocToggle = document.getElementById('toggle-toc');
if (tocToggle != null) {
  
  tocToggle.addEventListener('click', function(event) {
    var rect = tocToggle.getBoundingClientRect();
    modal = document.getElementById('modal-toc');
    modal.classList.toggle('visible');
    modal.style.left = Number(rect.left) + 5 + 'px';
    
    window.addEventListener('scroll', closeOnScroll);
    document.addEventListener('click', closeOnClick);
  });
  /*
  function closeOnScroll(e) {
    modal.classList.remove('visible');
    this.removeEventListener('scroll', closeOnScroll);
  }
  
  function closeOnClick(e) {
    console.log(e);
    console.log(e.target);
    var isClickInside = modal.contains(e.target);
    var isOpenButton = tocToggle.contains(e.target);
    if (!isClickInside && !isOpenButton) {
      modal.classList.remove('visible');
      this.removeEventListener('click', closeOnClick);
    }
  }
  */
  
}

/*--------------------------------------------------
  Reading Settings
  - Toggles the reading settings modal, with night mode, font size adjustment, etc.
--------------------------------------------------*/

var stylesettings = document.getElementById('settings-reading');
if (stylesettings != null) {
  
  stylesettings.addEventListener('click', function(event) {
    /* Position and expose the settings modal based on the stylesettings button */
    var rect = stylesettings.getBoundingClientRect();
    modal = document.getElementById('modal-reading');
    modal.classList.toggle('visible');
    modal.style.right = Number(document.body.clientWidth) - Number(rect.right) + 5 + 'px';
    
    window.addEventListener('scroll', closeOnScroll);
    document.addEventListener('click', closeOnClick);
  });
  
  function closeOnScroll(e) {
    modal.classList.remove('visible');
    this.removeEventListener('scroll', closeOnScroll);
  }
  
  function closeOnClick(e) {
    var isClickInside = modal.contains(e.target);
    var isOpenButton = stylesettings.contains(e.target);
    if (!isClickInside && !isOpenButton) {
      modal.classList.remove('visible');
      this.removeEventListener('click', closeOnClick);
    }
  }
}

/*--------------------------------------------------
  Theme Setting
  - Adds a "night" class to the body and stores the night setting within localstorage
--------------------------------------------------*/

var nightMode = document.getElementsByClassName('theme-night');
if (nightMode != null) {
  for (var i=0; i<nightMode.length; i++) {
    nightMode[i].addEventListener('click', function(event) {
      if (localStorage.getItem('theme') == null) {
        localStorage.setItem('theme', 'night');
        document.body.classList.add('night');
        document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: On';
      } else {
        localStorage.removeItem('theme');
        document.body.classList.remove('night');
        document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: Off';
      }
    });
  }
}

function checkTheme() {
  if (localStorage.getItem('theme')) {
    document.body.classList.add(localStorage.getItem('theme'));
    document.getElementsByClassName('theme-night')[0].innerHTML = 'Night Mode: On';
  }
}

/*--------------------------------------------------
  Font Family Setting
  - Adjusts the font family used throughout the interface
--------------------------------------------------*/

var fontFamily = document.getElementsByClassName('font-family');
if (fontFamily != null) {
  for (var i=0; i<fontFamily.length; i++) {
    fontFamily[i].addEventListener('click', function(event) {
      if (localStorage.getItem('fontFamily') == null) {
        setFontFamily('dyslexic');
      } else {
        setFontFamily(null);
      }
    });
  }
}

function setFontFamily(name) {
  if (name != null) {
    var currentFontFamily = window.getComputedStyle(document.body).getPropertyValue('font-family');
    document.body.style.fontFamily = name;
    localStorage.setItem('fontFamily', name);
    document.getElementsByClassName('font-family')[0].innerHTML = 'Dyslexia: On';
  } else {
    document.body.style.fontFamily = null;
    localStorage.removeItem('fontFamily');
    document.getElementsByClassName('font-family')[0].innerHTML = 'Dyslexia: Off';
  }

}

function checkFontFamily() {
  if (localStorage.getItem('fontFamily')) {
    document.body.style.fontFamily = localStorage.getItem('fontFamily');
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
  if (localStorage.getItem('fontSize')) {
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














/*
var allButtons = document.querySelectorAll('button');
for (var i = 0; i < allButtons.length; i++) {
  
  allButtons[i].addEventListener('click', function() {
        
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
*/

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

function closeOnOutsideClick() {
  
  var firstClick = document.firstClick; // The first clicked element
  var newClick = event.target;          // The newly-clicked element
  
  if (!isDescendant(firstClick, newClick)) {
    console.log('outside click');
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
    if (target.nextElementSibling) {
      modal.querySelector('[name="subtitle"]').value = target.nextElementSibling.innerHTML;
    }
  }
  // Update nav item
  var button = modal.querySelector('[data-action="updatenav"]');
  if (button) {
    
    function updateItem() {
      target.innerText = modal.querySelector('[name="title"]').value;
      target.setAttribute('data-href', modal.querySelector('[name="url"]').value);
      if (target.nextElementSibling) {
        target.nextElementSibling.innerHTML = modal.querySelector('[name="subtitle"]').value;
      }
      if (!target.nextElementSibling && modal.querySelector('[name="subtitle"]').value != '') {
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
  document.body.classList.add('noscroll');
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
















/* Adds a class to modals when opened */
/*
var modals = document.getElementsByClassName('modal');
if (modals != null) {
  for (var i = 0; i < modals.length; i++) {
    
    // Add click listener to previous element
    modals[i].previousElementSibling.addEventListener('click', function(event) {
      var target = getEventTarget(event);
      target.nextElementSibling.classList.add('visible');
      document.body.classList.add('noscroll');
    }, false);
    
    // Add click listener to background
    modals[i].firstElementChild.addEventListener('click', function(event) {
      var target = getEventTarget(event);
      target.parentNode.classList.remove('visible');
      document.body.classList.remove('noscroll');
    }, false);
    
  }
}
*/

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


/*
var response = '';

function uploadHandler(type) {
  
  if (type == 'file') { var input = document.getElementById('fileToUpload') };
  if (type == 'hero') { var input = document.getElementById('heroToUpload') };
  
  input.click();
  
  
    return new Promise (function (resolve, reject) {
      input.onchange = function(event) {
        var data = new FormData();
        var files = input.files;
        for (var i = 0; i < files.length; i++) {
          var file = files[i];
          data.append('files', file, file.name);
        }
        data.append('page', window.location.pathname);
        data.append('type', type);
        
        var request = new XMLHttpRequest();
        request.open('POST', 'uploadnew', true);
        request.onload = function () {
          if (request.status === 200) { // File uploaded
            response = JSON.parse(this.response);
            console.log('blablah' + response);
            return response;
          } else {
            //alert('An error occurred! Contact Andy Braren for help.');
          }
        };
        request.send(data);
      }
    });
  
}


function addFileTool() {
  
  // Forcefully insert the new tool
  var prev = document.getElementsByClassName('ct-tool--video')[0];
  html = '<div class="ct-tool ct-tool--file ct-tool--disabled tool-file" data-ct-tooltip="File"></div>';
  prev.insertAdjacentHTML('afterend', html);
  
  var tool = document.getElementsByClassName('tool-file')[0];
  var focus = document.getElementsByClassName('ce-element--focused')[0];
  
  response = uploadHandler('file')
              .then(function (e) {
                console.log("succes");
                insertFile(response.filename, response.fileurl, response.extension);
              }, function (e) {
                console.log("errorrrrr");
              });
  
  
  tool.addEventListener('click', function(event) {
    var promise = new Promise(function(resolve, reject) {
      response = uploadHandler('file');
      if (response !== undefined) {
        resolve(response);
      } else {
        reject(Error("Broked"));
      }
    });
    
    promise.then(function(result) {
      console.log(result);
    }, function(err) {
      console.log(err);
    });
  });
  
  console.log("hello" + response);
  
  if (response != undefined) {
    insertFile(response.filename, response.fileurl, response.extension);
  }
    
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
    p = new ContentEdit.Text('p', {}, html)
    selectedElm.parent().attach(p, selectedElm.parent().children.indexOf(selectedElm) + 1)
    p.focus()
    
    // New paragraph below the newly-added file
    selectedElm = ContentEdit.Root.get().focused();
    n = new ContentEdit.Text('p', {}, '')
    selectedElm.parent().attach(n, selectedElm.parent().children.indexOf(selectedElm) + 1)
    n.focus()
  }
}
*/









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


function toggleHero(clickcount, formupload) {
  var heroDiv = document.getElementById('hero');
  var heroAdd = document.getElementById('hero-add');
  var formHero = document.getElementById('heroToUpload');
  
  if (heroDiv != null) {
    if (clickcount == '0') {
      heroDiv.addEventListener('click', formclick = function() {
        formHero.click();
      });
      formHero.onchange = function() {
        
        var data = new FormData();
        var files = formHero.files;
        for (var i = 0; i < files.length; i++) {
          var file = files[i];
          data.append('files', file, file.name);
        }
        data.append('page', window.location.pathname);
        
        data.append('type', 'hero');
        
        var request = new XMLHttpRequest();
        request.open('POST', 'upload', true);
        request.onload = function () {
          if (request.status === 200) { // File uploaded
            response = JSON.parse(this.response);
            insertHero(response.filename, response.fileurl, response.extension);
          } else {
            //alert('An error occurred! Contact Andy Braren for help.');
          }
        };
        request.send(data);
        
        function insertHero(filename, fileurl, extension) {
          var heroAdd = document.getElementById('hero-add');
          if (heroAdd != undefined) {
            html = '<figure><img src="' + fileurl + '"></img></figure>';
            heroAdd.insertAdjacentHTML('afterend', html);
            heroAdd.parentNode.removeChild(heroAdd);
          } else {
            image = heroDiv.getElementsByTagName('IMG')[0];
            image.removeAttribute('class');
            image.parentNode.removeAttribute('style');
            image.src = fileurl;
          }
        }
        
      };
    }
    else {
      heroDiv.removeEventListener('click', formclick);
    }
  }
  /*
  if (heroAdd != null) {
    if (clickcount == '0') {
      heroAdd.addEventListener('click', formclick = function() {
        formHero.click();
      });
      formHero.onchange = function() {
        savePage();
        editor.ignition().confirm();
        formupload.submit();
      };
    }
    else {
      heroAdd.removeEventListener('click', formclick);
    }
  }
  */
}

/*
function toggleHero(clickcount, formupload) {
  var heroDiv = document.getElementById('hero');
  var heroAdd = document.getElementById('hero-add');
  var formHero = document.getElementById('heroToUpload');
  
  if (heroDiv != null) {
    if (clickcount == '0') {
      heroDiv.addEventListener('click', formclick = function() {
        formHero.click();
      });
      formHero.onchange = function() {
        savePage();
        editor.ignition().confirm();
        formupload.submit();
      };
    }
    else {
      heroDiv.removeEventListener('click', formclick);
    }
  }
  
  if (heroAdd != null) {
    if (clickcount == '0') {
      heroAdd.addEventListener('click', formclick = function() {
        formHero.click();
      });
      formHero.onchange = function() {
        savePage();
        editor.ignition().confirm();
        formupload.submit();
      };
    }
    else {
      heroAdd.removeEventListener('click', formclick);
    }
  }
}
*/

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
  
  dragula([document.getElementById('authors')]);
  dragula([document.getElementById('groups')]);
  
  dragula([document.getElementsByClassName('menu')[0]]);
  dragula([document.getElementsByClassName('menu')[1]]);
  dragula([document.getElementsByClassName('menu')[2]]);
  
  dragula([document.getElementsByClassName('subnodes')[0]]);
  
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
  
  
  
  /*
  var widgets = document.getElementsByClassName('widget');
  for (var i = 0; i < widgets.length; i++) {
    var items = widgets[i].getElementsByClassName('item');
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
  */
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
          
          if (redirecturl) {
            window.location.href = redirecturl;
          }
          if (changeurl) {
            history.replaceState(null, null, changeurl);
            document.title = document.querySelector('h1').innerText;
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
  /*
  var authors = document.getElementById('authors');
  if (authors != null) {
    var authors = Array.prototype.slice.call(authors.children);
    var arr = [];
    for (var i = authors.length - 1; i >= 0; i--) {
      arr.push(authors[i].getAttribute('data-username'));
    }
    var authors = arr.reverse().join(', ');
    data.append('authors', authors);
  }
  */
  
  /* Users */
  var users = document.getElementById('users');
  if (users != null) {
    var users = Array.prototype.slice.call(users.children);
    var arr = [];
    for (var i = users.length - 1; i >= 0; i--) {
      arr.push(users[i].getAttribute('data-slug'));
    }
    var users = arr.reverse().join(', ');
    data.append('users', users);
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
  
  /* Navigation */
  var nav = document.querySelectorAll('.menu')[0];
  if (nav != null) {
    
    var arr = [];
    var items = nav.querySelectorAll('li');
    for (var i = 0; i < items.length; i++) {
      
      var element = items[i].querySelector('a');
      var obj = {};
      
      obj['title'] = element.innerText;
      obj['href'] = element.href.split('/').pop();
      
      if (element.nextElementSibling) {
        obj['subtitle'] = element.nextElementSibling.innerText;
      }
      
      // if active and submenu is present, create those
      if (items[i].classList) {
        if (items[i].classList.contains('active')) {
          if (document.querySelector('#subnavigation')) {
            var subNavItems = document.querySelector('#subnavigation').querySelectorAll('.menu')[0].querySelectorAll('a');
            var arr2 = [];
            
            for (var e = 0; e < subNavItems.length; e++) {
              var obj2 = {};
              obj2['title'] = subNavItems[e].innerText;
              if (subNavItems[e].href) {
                obj2['href']  = subNavItems[e].href.split('/').pop();
              }
              arr2.push(obj2);
            }
            obj['sub'] = arr2;
          }
        }
      }
      
      arr.push(obj);
    }
    data.append('menusecondary', JSON.stringify(arr));
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
    clearSearch(authorresults);
    authorfield.value = '';
    authorfield.classList.remove('clicked');
    
    if (this.getAttribute('data-major') != 'undefined') {
      var major = '<span>' + this.getAttribute('data-major') + '</span>';
    } else {
      var major = '';
    }
    var resultHTML = '<a data-slug="' + this.getAttribute('data-slug') + '" data-href="' + this.getAttribute('data-profileURL') + '" ><div class="item-delete"></div><div class="row"><img src="' + this.getAttribute('data-avatar') + '" class="' + this.getAttribute('data-color') + '" width="40" height="40"><div class="column"><span>' + this.getAttribute('data-name') + '</span>' + major + '</div></div></a>';
    authorwidget.insertAdjacentHTML('beforeend', resultHTML);
    itemDeleteButtons();
  }
  
}

/* Add group widget
*/
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



/*
var main = document.getElementsByTagName('main')[0];
var filters = document.getElementById('filters');
if (filters != null) {
  
  var selects = filters.getElementsByTagName('select');
  var inputs = filters.getElementsByTagName('input');
  var allTags = [];
  allTags.push.apply(allTags, selects);
  allTags.push.apply(allTags, inputs);
  
  for (var i=0; i<allTags.length; i++) {
    allTags[i].addEventListener('click', function(event) {
      var initialvalue = getEventTarget(event).value;
      this.onchange = function() {
        main.removeAttribute('data-filters', initialvalue);
        main.setAttribute('data-filters', this.value);
      }
    }, false);
  }
}
*/






















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

/* Solution toggle */
/*
$('.button-solution').on('click', function(event) {
  $(event.target).next().toggleClass("visibleflex");
});

$('.button-edit').on('click', function(event) {
  $(event.target).next().toggleClass("visibleflex");
});
*/

/* Solution toggle */
/* Loop through every solution button, add a listener, and toggle the solution class when clicked */
/*
function getEventTarget(e) {
  e = e || window.event;
  return e.target || e.srcElement;
}
var solutionbutton = document.getElementsByClassName('solution-button');
for (var i = 0; i < solutionbutton.length; i++) {
  solutionbutton[i].addEventListener('click', function() {
    alert('Hello world');
    event.target
  }, false);
}
*/

/* Menu button toggle */
/*
$('.menu-button').on('click', function(event) {
  $('nav').toggleClass('visibleflex animated fadeinleft');
});
*/






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
    openEdit();
    
    history.replaceState({}, document.title, window.location.href.split('#')[0]);
    
  }
}

window.addEventListener('load', function(event) {
  positionToolbox();
  checkIfNewPage();
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







/* DRAGULA
-------------------------------------------------- */
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var n;n="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,n.dragula=e()}}(function(){return function e(n,t,r){function o(u,c){if(!t[u]){if(!n[u]){var a="function"==typeof require&&require;if(!c&&a)return a(u,!0);if(i)return i(u,!0);var f=new Error("Cannot find module '"+u+"'");throw f.code="MODULE_NOT_FOUND",f}var l=t[u]={exports:{}};n[u][0].call(l.exports,function(e){var t=n[u][1][e];return o(t?t:e)},l,l.exports,e,n,t,r)}return t[u].exports}for(var i="function"==typeof require&&require,u=0;u<r.length;u++)o(r[u]);return o}({1:[function(e,n,t){"use strict";function r(e){var n=u[e];return n?n.lastIndex=0:u[e]=n=new RegExp(c+e+a,"g"),n}function o(e,n){var t=e.className;t.length?r(n).test(t)||(e.className+=" "+n):e.className=n}function i(e,n){e.className=e.className.replace(r(n)," ").trim()}var u={},c="(?:^|\\s)",a="(?:\\s|$)";n.exports={add:o,rm:i}},{}],2:[function(e,n,t){(function(t){"use strict";function r(e,n){function t(e){return-1!==le.containers.indexOf(e)||fe.isContainer(e)}function r(e){var n=e?"remove":"add";o(S,n,"mousedown",O),o(S,n,"mouseup",L)}function c(e){var n=e?"remove":"add";o(S,n,"mousemove",N)}function m(e){var n=e?"remove":"add";w[n](S,"selectstart",C),w[n](S,"click",C)}function h(){r(!0),L({})}function C(e){ce&&e.preventDefault()}function O(e){ne=e.clientX,te=e.clientY;var n=1!==i(e)||e.metaKey||e.ctrlKey;if(!n){var t=e.target,r=T(t);r&&(ce=r,c(),"mousedown"===e.type&&(p(t)?t.focus():e.preventDefault()))}}function N(e){if(ce){if(0===i(e))return void L({});if(void 0===e.clientX||e.clientX!==ne||void 0===e.clientY||e.clientY!==te){if(fe.ignoreInputTextSelection){var n=y("clientX",e),t=y("clientY",e),r=x.elementFromPoint(n,t);if(p(r))return}var o=ce;c(!0),m(),D(),B(o);var a=u(W);Z=y("pageX",e)-a.left,ee=y("pageY",e)-a.top,E.add(ie||W,"gu-transit"),K(),U(e)}}}function T(e){if(!(le.dragging&&J||t(e))){for(var n=e;v(e)&&t(v(e))===!1;){if(fe.invalid(e,n))return;if(e=v(e),!e)return}var r=v(e);if(r&&!fe.invalid(e,n)){var o=fe.moves(e,r,n,g(e));if(o)return{item:e,source:r}}}}function X(e){return!!T(e)}function Y(e){var n=T(e);n&&B(n)}function B(e){$(e.item,e.source)&&(ie=e.item.cloneNode(!0),le.emit("cloned",ie,e.item,"copy")),Q=e.source,W=e.item,re=oe=g(e.item),le.dragging=!0,le.emit("drag",W,Q)}function P(){return!1}function D(){if(le.dragging){var e=ie||W;M(e,v(e))}}function I(){ce=!1,c(!0),m(!0)}function L(e){if(I(),le.dragging){var n=ie||W,t=y("clientX",e),r=y("clientY",e),o=a(J,t,r),i=q(o,t,r);i&&(ie&&fe.copySortSource||!ie||i!==Q)?M(n,i):fe.removeOnSpill?R():A()}}function M(e,n){var t=v(e);ie&&fe.copySortSource&&n===Q&&t.removeChild(W),k(n)?le.emit("cancel",e,Q,Q):le.emit("drop",e,n,Q,oe),j()}function R(){if(le.dragging){var e=ie||W,n=v(e);n&&n.removeChild(e),le.emit(ie?"cancel":"remove",e,n,Q),j()}}function A(e){if(le.dragging){var n=arguments.length>0?e:fe.revertOnSpill,t=ie||W,r=v(t),o=k(r);o===!1&&n&&(ie?r&&r.removeChild(ie):Q.insertBefore(t,re)),o||n?le.emit("cancel",t,Q,Q):le.emit("drop",t,r,Q,oe),j()}}function j(){var e=ie||W;I(),z(),e&&E.rm(e,"gu-transit"),ue&&clearTimeout(ue),le.dragging=!1,ae&&le.emit("out",e,ae,Q),le.emit("dragend",e),Q=W=ie=re=oe=ue=ae=null}function k(e,n){var t;return t=void 0!==n?n:J?oe:g(ie||W),e===Q&&t===re}function q(e,n,r){function o(){var o=t(i);if(o===!1)return!1;var u=H(i,e),c=V(i,u,n,r),a=k(i,c);return a?!0:fe.accepts(W,i,Q,c)}for(var i=e;i&&!o();)i=v(i);return i}function U(e){function n(e){le.emit(e,f,ae,Q)}function t(){s&&n("over")}function r(){ae&&n("out")}if(J){e.preventDefault();var o=y("clientX",e),i=y("clientY",e),u=o-Z,c=i-ee;J.style.left=u+"px",J.style.top=c+"px";var f=ie||W,l=a(J,o,i),d=q(l,o,i),s=null!==d&&d!==ae;(s||null===d)&&(r(),ae=d,t());var p=v(f);if(d===Q&&ie&&!fe.copySortSource)return void(p&&p.removeChild(f));var m,h=H(d,l);if(null!==h)m=V(d,h,o,i);else{if(fe.revertOnSpill!==!0||ie)return void(ie&&p&&p.removeChild(f));m=re,d=Q}(null===m&&s||m!==f&&m!==g(f))&&(oe=m,d.insertBefore(f,m),le.emit("shadow",f,d,Q))}}function _(e){E.rm(e,"gu-hide")}function F(e){le.dragging&&E.add(e,"gu-hide")}function K(){if(!J){var e=W.getBoundingClientRect();J=W.cloneNode(!0),J.style.width=d(e)+"px",J.style.height=s(e)+"px",E.rm(J,"gu-transit"),E.add(J,"gu-mirror"),fe.mirrorContainer.appendChild(J),o(S,"add","mousemove",U),E.add(fe.mirrorContainer,"gu-unselectable"),le.emit("cloned",J,W,"mirror")}}function z(){J&&(E.rm(fe.mirrorContainer,"gu-unselectable"),o(S,"remove","mousemove",U),v(J).removeChild(J),J=null)}function H(e,n){for(var t=n;t!==e&&v(t)!==e;)t=v(t);return t===S?null:t}function V(e,n,t,r){function o(){var n,o,i,u=e.children.length;for(n=0;u>n;n++){if(o=e.children[n],i=o.getBoundingClientRect(),c&&i.left+i.width/2>t)return o;if(!c&&i.top+i.height/2>r)return o}return null}function i(){var e=n.getBoundingClientRect();return u(c?t>e.left+d(e)/2:r>e.top+s(e)/2)}function u(e){return e?g(n):n}var c="horizontal"===fe.direction,a=n!==e?i():o();return a}function $(e,n){return"boolean"==typeof fe.copy?fe.copy:fe.copy(e,n)}var G=arguments.length;1===G&&Array.isArray(e)===!1&&(n=e,e=[]);var J,Q,W,Z,ee,ne,te,re,oe,ie,ue,ce,ae=null,fe=n||{};void 0===fe.moves&&(fe.moves=l),void 0===fe.accepts&&(fe.accepts=l),void 0===fe.invalid&&(fe.invalid=P),void 0===fe.containers&&(fe.containers=e||[]),void 0===fe.isContainer&&(fe.isContainer=f),void 0===fe.copy&&(fe.copy=!1),void 0===fe.copySortSource&&(fe.copySortSource=!1),void 0===fe.revertOnSpill&&(fe.revertOnSpill=!1),void 0===fe.removeOnSpill&&(fe.removeOnSpill=!1),void 0===fe.direction&&(fe.direction="vertical"),void 0===fe.ignoreInputTextSelection&&(fe.ignoreInputTextSelection=!0),void 0===fe.mirrorContainer&&(fe.mirrorContainer=x.body);var le=b({containers:fe.containers,start:Y,end:D,cancel:A,remove:R,destroy:h,canMove:X,dragging:!1});return fe.removeOnSpill===!0&&le.on("over",_).on("out",F),r(),le}function o(e,n,r,o){var i={mouseup:"touchend",mousedown:"touchstart",mousemove:"touchmove"},u={mouseup:"pointerup",mousedown:"pointerdown",mousemove:"pointermove"},c={mouseup:"MSPointerUp",mousedown:"MSPointerDown",mousemove:"MSPointerMove"};t.navigator.pointerEnabled?w[n](e,u[r],o):t.navigator.msPointerEnabled?w[n](e,c[r],o):(w[n](e,i[r],o),w[n](e,r,o))}function i(e){if(void 0!==e.touches)return e.touches.length;if(void 0!==e.which&&0!==e.which)return e.which;if(void 0!==e.buttons)return e.buttons;var n=e.button;return void 0!==n?1&n?1:2&n?3:4&n?2:0:void 0}function u(e){var n=e.getBoundingClientRect();return{left:n.left+c("scrollLeft","pageXOffset"),top:n.top+c("scrollTop","pageYOffset")}}function c(e,n){return"undefined"!=typeof t[n]?t[n]:S.clientHeight?S[e]:x.body[e]}function a(e,n,t){var r,o=e||{},i=o.className;return o.className+=" gu-hide",r=x.elementFromPoint(n,t),o.className=i,r}function f(){return!1}function l(){return!0}function d(e){return e.width||e.right-e.left}function s(e){return e.height||e.bottom-e.top}function v(e){return e.parentNode===x?null:e.parentNode}function p(e){return"INPUT"===e.tagName||"TEXTAREA"===e.tagName||"SELECT"===e.tagName||m(e)}function m(e){return e?"false"===e.contentEditable?!1:"true"===e.contentEditable?!0:m(v(e)):!1}function g(e){function n(){var n=e;do n=n.nextSibling;while(n&&1!==n.nodeType);return n}return e.nextElementSibling||n()}function h(e){return e.targetTouches&&e.targetTouches.length?e.targetTouches[0]:e.changedTouches&&e.changedTouches.length?e.changedTouches[0]:e}function y(e,n){var t=h(n),r={pageX:"clientX",pageY:"clientY"};return e in r&&!(e in t)&&r[e]in t&&(e=r[e]),t[e]}var b=e("contra/emitter"),w=e("crossvent"),E=e("./classes"),x=document,S=x.documentElement;n.exports=r}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./classes":1,"contra/emitter":5,crossvent:6}],3:[function(e,n,t){n.exports=function(e,n){return Array.prototype.slice.call(e,n)}},{}],4:[function(e,n,t){"use strict";var r=e("ticky");n.exports=function(e,n,t){e&&r(function(){e.apply(t||null,n||[])})}},{ticky:9}],5:[function(e,n,t){"use strict";var r=e("atoa"),o=e("./debounce");n.exports=function(e,n){var t=n||{},i={};return void 0===e&&(e={}),e.on=function(n,t){return i[n]?i[n].push(t):i[n]=[t],e},e.once=function(n,t){return t._once=!0,e.on(n,t),e},e.off=function(n,t){var r=arguments.length;if(1===r)delete i[n];else if(0===r)i={};else{var o=i[n];if(!o)return e;o.splice(o.indexOf(t),1)}return e},e.emit=function(){var n=r(arguments);return e.emitterSnapshot(n.shift()).apply(this,n)},e.emitterSnapshot=function(n){var u=(i[n]||[]).slice(0);return function(){var i=r(arguments),c=this||e;if("error"===n&&t["throws"]!==!1&&!u.length)throw 1===i.length?i[0]:i;return u.forEach(function(r){t.async?o(r,i,c):r.apply(c,i),r._once&&e.off(n,r)}),e}},e}},{"./debounce":4,atoa:3}],6:[function(e,n,t){(function(t){"use strict";function r(e,n,t,r){return e.addEventListener(n,t,r)}function o(e,n,t){return e.attachEvent("on"+n,f(e,n,t))}function i(e,n,t,r){return e.removeEventListener(n,t,r)}function u(e,n,t){var r=l(e,n,t);return r?e.detachEvent("on"+n,r):void 0}function c(e,n,t){function r(){var e;return p.createEvent?(e=p.createEvent("Event"),e.initEvent(n,!0,!0)):p.createEventObject&&(e=p.createEventObject()),e}function o(){return new s(n,{detail:t})}var i=-1===v.indexOf(n)?o():r();e.dispatchEvent?e.dispatchEvent(i):e.fireEvent("on"+n,i)}function a(e,n,r){return function(n){var o=n||t.event;o.target=o.target||o.srcElement,o.preventDefault=o.preventDefault||function(){o.returnValue=!1},o.stopPropagation=o.stopPropagation||function(){o.cancelBubble=!0},o.which=o.which||o.keyCode,r.call(e,o)}}function f(e,n,t){var r=l(e,n,t)||a(e,n,t);return h.push({wrapper:r,element:e,type:n,fn:t}),r}function l(e,n,t){var r=d(e,n,t);if(r){var o=h[r].wrapper;return h.splice(r,1),o}}function d(e,n,t){var r,o;for(r=0;r<h.length;r++)if(o=h[r],o.element===e&&o.type===n&&o.fn===t)return r}var s=e("custom-event"),v=e("./eventmap"),p=t.document,m=r,g=i,h=[];t.addEventListener||(m=o,g=u),n.exports={add:m,remove:g,fabricate:c}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./eventmap":7,"custom-event":8}],7:[function(e,n,t){(function(e){"use strict";var t=[],r="",o=/^on/;for(r in e)o.test(r)&&t.push(r.slice(2));n.exports=t}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],8:[function(e,n,t){(function(e){function t(){try{var e=new r("cat",{detail:{foo:"bar"}});return"cat"===e.type&&"bar"===e.detail.foo}catch(n){}return!1}var r=e.CustomEvent;n.exports=t()?r:"function"==typeof document.createEvent?function(e,n){var t=document.createEvent("CustomEvent");return n?t.initCustomEvent(e,n.bubbles,n.cancelable,n.detail):t.initCustomEvent(e,!1,!1,void 0),t}:function(e,n){var t=document.createEventObject();return t.type=e,n?(t.bubbles=Boolean(n.bubbles),t.cancelable=Boolean(n.cancelable),t.detail=n.detail):(t.bubbles=!1,t.cancelable=!1,t.detail=void 0),t}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],9:[function(e,n,t){var r,o="function"==typeof setImmediate;r=o?function(e){setImmediate(e)}:function(e){setTimeout(e,0)},n.exports=r},{}]},{},[2])(2)});

/* Frend tooltips
  - http://frend.co/components/tooltip/
*/
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.Frtooltip=e()}}(function(){return function e(t,r,n){function o(u,a){if(!r[u]){if(!t[u]){var l="function"==typeof require&&require;if(!a&&l)return l(u,!0);if(i)return i(u,!0);var s=new Error("Cannot find module '"+u+"'");throw s.code="MODULE_NOT_FOUND",s}var c=r[u]={exports:{}};t[u][0].call(c.exports,function(e){var r=t[u][1][e];return o(r?r:e)},c,c.exports,e,t,r,n)}return r[u].exports}for(var i="function"==typeof require&&require,u=0;u<n.length;u++)o(n[u]);return o}({1:[function(e,t,r){"use strict";Object.defineProperty(r,"__esModule",{value:!0}),NodeList.prototype.forEach=Array.prototype.forEach,Element.prototype.matches=Element.prototype.matches||Element.prototype.mozMatchesSelector||Element.prototype.msMatchesSelector||Element.prototype.oMatchesSelector||Element.prototype.webkitMatchesSelector;var n=function(){function e(e){"function"==typeof e&&setTimeout(e,0)}function t(e,t){for(;e&&!e.matches(t);)e=e.parentElement;return e}function r(e,t){var r=e.querySelector(x),n=e.querySelector(S),o=M.createElement("button");o.setAttribute("class",r.getAttribute("class")),o.setAttribute("aria-expanded","false"),o.setAttribute("aria-describedby",""),o.textContent=r.textContent,e.replaceChild(o,r),n.setAttribute("role","tooltip"),n.setAttribute("id",w+"-"+t),n.setAttribute("aria-hidden","true"),n.setAttribute("aria-live","polite")}function n(e){var t=e.querySelector(x),r=e.querySelector(S),n=M.createElement("span");n.setAttribute("class",t.getAttribute("class")),n.textContent=t.textContent,e.replaceChild(n,t),r.removeAttribute("role"),r.removeAttribute("id"),r.removeAttribute("aria-hidden"),r.removeAttribute("aria-live")}function o(t,r){var n=r.getAttribute("id");t.setAttribute("aria-describedby",n),t.setAttribute("aria-expanded","true"),r.setAttribute("aria-hidden","false"),_=r,e(f),e(p)}function i(e,t){e.setAttribute("aria-describedby",""),e.setAttribute("aria-expanded","false"),t.setAttribute("aria-hidden","true"),_=null,m(),b()}function u(){O.forEach(function(e,t){n(e,t),v(e),e.classList.remove(k)}),_=null,m(),b()}function a(e){_&&i(_.previousElementSibling,_);var t=e.target,r=t.nextElementSibling;"false"===t.getAttribute("aria-expanded")?o(t,r):i(t,r)}function l(){_&&i(_.previousElementSibling,_)}function s(e){var r=e.target===_,n=t(e.target,S);r||n||i(_.previousElementSibling,_)}function c(e){27===e.keyCode&&i(_.previousElementSibling,_)}function d(e){var t=e.querySelector(x);t.addEventListener("click",a),t.addEventListener("mouseenter",a),t.addEventListener("mouseleave",l)}function f(){M.addEventListener("click",s),M.addEventListener("touchstart",s)}function p(){M.addEventListener("keydown",c)}function v(e){var t=e.querySelector(x);t.removeEventListener("click",a),t.removeEventListener("mouseenter",a),t.removeEventListener("mouseleave",l)}function m(){M.removeEventListener("click",s),M.removeEventListener("touchstart",s)}function b(){M.removeEventListener("keydown",c)}function y(){O&&O.forEach(function(e,t){r(e,t),d(e),e.classList.add(k)})}var E=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],A=E.selector,h=void 0===A?".js-fr-tooltip":A,g=E.tooltipSelector,S=void 0===g?".js-fr-tooltip-tooltip":g,L=E.toggleSelector,x=void 0===L?".js-fr-tooltip-toggle":L,q=E.tooltipIdPrefix,w=void 0===q?"tooltip":q,C=E.readyClass,k=void 0===C?"fr-tooltip--is-ready":C,M=document,j=M.documentElement;if("querySelector"in M&&"addEventListener"in window&&j.classList){var O=M.querySelectorAll(h),_=null;return y(),{init:y,destroy:u}}};r["default"]=n,t.exports=r["default"]},{}]},{},[1])(1)});

var myTooltip = Frtooltip({
	// String - Container selector, hook for JS init() method
	selector: '.js-fr-tooltip',

	// String - Selector to define the tooltip element
	tooltipSelector: '.js-fr-tooltip-tooltip',

	// String - Selector to define the toggle element controlling the tooltip
	toggleSelector: '.js-fr-tooltip-toggle',

	// String - Prefix for the id applied to each tooltip as a reference for the toggle
	tooltipIdPrefix: 'tooltip',

	// String - Class name that will be added to the selector when the component has been initialised
	readyClass: 'fr-tooltip--is-ready'
});

/*!
 * object-fit-images - An object-fit polyfill for IE/Edge until they support it
 * https://github.com/bfred-it/object-fit-images
 */
 
var objectFitImages=function(){"use strict";var e="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";var t=/(object-fit|object-position)\s*:\s*([^;$"'\s]+)/g;var i="object-fit"in document.createElement("i").style;var n=false;function r(e){var i=getComputedStyle(e).fontFamily;var n;var r={};while((n=t.exec(i))!==null){r[n[1]]=n[2]}return r}function o(t,i){var n=r(t);if(!n["object-fit"]||n["object-fit"]==="fill"){return}i=i||t.currentSrc||t.src;if(t.srcset){t.srcset=""}if(!t[e]){t.src=e;a(t)}t[e]=t[e]||{s:i};t.style.backgroundImage="url("+i+")";t.style.backgroundPosition=n["object-position"]||"center";t.style.backgroundRepeat="no-repeat";if(n["object-fit"].indexOf("scale-down")<0){t.style.backgroundSize=n["object-fit"].replace("none","auto")}else{if(!t[e].i){t[e].i=new Image;t[e].i.src=i}(function o(){if(t[e].i.naturalWidth){if(t[e].i.naturalWidth>t.width||t[e].i.naturalHeight>t.height){t.style.backgroundSize="contain"}else{t.style.backgroundSize="auto"}return}setTimeout(o,100)})()}}function a(t){var i={get:function(){return t[e].s},set:function(i){delete t[e].i;return o(t,i)}};Object.defineProperty(t,"src",i);Object.defineProperty(t,"currentSrc",{get:i.get})}function c(e){window.addEventListener("resize",f.bind(null,e))}function u(e){if(e.target.tagName==="IMG"){o(e.target)}}function f(e,t){if(i){return false}var r=!n&&!e;t=t||{};e=e||"img";if(typeof e==="string"){e=document.querySelectorAll("img")}else if(!e.length){e=[e]}for(var a=0;a<e.length;a++){o(e[a])}if(r){document.body.addEventListener("load",u,true);n=true;e="img"}if(t.watchMQ){c(e)}}return f}();

/*!
 * headroom.js v0.8.0 - Give your page some headroom. Hide your header until you need it
 * Copyright (c) 2016 Nick Williams - http://wicky.nillia.ms/headroom.js
 * License: MIT
 */
!function(a,b){"use strict";"function"==typeof define&&define.amd?define([],b):"object"==typeof exports?module.exports=b():a.Headroom=b()}(this,function(){"use strict";function a(a){this.callback=a,this.ticking=!1}function b(a){return a&&"undefined"!=typeof window&&(a===window||a.nodeType)}function c(a){if(arguments.length<=0)throw new Error("Missing arguments in extend function");var d,e,f=a||{};for(e=1;e<arguments.length;e++){var g=arguments[e]||{};for(d in g)"object"!=typeof f[d]||b(f[d])?f[d]=f[d]||g[d]:f[d]=c(f[d],g[d])}return f}function d(a){return a===Object(a)?a:{down:a,up:a}}function e(a,b){b=c(b,e.options),this.lastKnownScrollY=0,this.elem=a,this.tolerance=d(b.tolerance),this.classes=b.classes,this.offset=b.offset,this.scroller=b.scroller,this.initialised=!1,this.onPin=b.onPin,this.onUnpin=b.onUnpin,this.onTop=b.onTop,this.onNotTop=b.onNotTop,this.onBottom=b.onBottom,this.onNotBottom=b.onNotBottom}var f={bind:!!function(){}.bind,classList:"classList"in document.documentElement,rAF:!!(window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame)};return window.requestAnimationFrame=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame,a.prototype={constructor:a,update:function(){this.callback&&this.callback(),this.ticking=!1},requestTick:function(){this.ticking||(requestAnimationFrame(this.rafCallback||(this.rafCallback=this.update.bind(this))),this.ticking=!0)},handleEvent:function(){this.requestTick()}},e.prototype={constructor:e,init:function(){return e.cutsTheMustard?(this.debouncer=new a(this.update.bind(this)),this.elem.classList.add(this.classes.initial),setTimeout(this.attachEvent.bind(this),100),this):void 0},destroy:function(){var a=this.classes;this.initialised=!1,this.elem.classList.remove(a.unpinned,a.pinned,a.top,a.notTop,a.initial),this.scroller.removeEventListener("scroll",this.debouncer,!1)},attachEvent:function(){this.initialised||(this.lastKnownScrollY=this.getScrollY(),this.initialised=!0,this.scroller.addEventListener("scroll",this.debouncer,!1),this.debouncer.handleEvent())},unpin:function(){var a=this.elem.classList,b=this.classes;!a.contains(b.pinned)&&a.contains(b.unpinned)||(a.add(b.unpinned),a.remove(b.pinned),this.onUnpin&&this.onUnpin.call(this))},pin:function(){var a=this.elem.classList,b=this.classes;a.contains(b.unpinned)&&(a.remove(b.unpinned),a.add(b.pinned),this.onPin&&this.onPin.call(this))},top:function(){var a=this.elem.classList,b=this.classes;a.contains(b.top)||(a.add(b.top),a.remove(b.notTop),this.onTop&&this.onTop.call(this))},notTop:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notTop)||(a.add(b.notTop),a.remove(b.top),this.onNotTop&&this.onNotTop.call(this))},bottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.bottom)||(a.add(b.bottom),a.remove(b.notBottom),this.onBottom&&this.onBottom.call(this))},notBottom:function(){var a=this.elem.classList,b=this.classes;a.contains(b.notBottom)||(a.add(b.notBottom),a.remove(b.bottom),this.onNotBottom&&this.onNotBottom.call(this))},getScrollY:function(){return void 0!==this.scroller.pageYOffset?this.scroller.pageYOffset:void 0!==this.scroller.scrollTop?this.scroller.scrollTop:(document.documentElement||document.body.parentNode||document.body).scrollTop},getViewportHeight:function(){return window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight},getElementPhysicalHeight:function(a){return Math.max(a.offsetHeight,a.clientHeight)},getScrollerPhysicalHeight:function(){return this.scroller===window||this.scroller===document.body?this.getViewportHeight():this.getElementPhysicalHeight(this.scroller)},getDocumentHeight:function(){var a=document.body,b=document.documentElement;return Math.max(a.scrollHeight,b.scrollHeight,a.offsetHeight,b.offsetHeight,a.clientHeight,b.clientHeight)},getElementHeight:function(a){return Math.max(a.scrollHeight,a.offsetHeight,a.clientHeight)},getScrollerHeight:function(){return this.scroller===window||this.scroller===document.body?this.getDocumentHeight():this.getElementHeight(this.scroller)},isOutOfBounds:function(a){var b=0>a,c=a+this.getScrollerPhysicalHeight()>this.getScrollerHeight();return b||c},toleranceExceeded:function(a,b){return Math.abs(a-this.lastKnownScrollY)>=this.tolerance[b]},shouldUnpin:function(a,b){var c=a>this.lastKnownScrollY,d=a>=this.offset;return c&&d&&b},shouldPin:function(a,b){var c=a<this.lastKnownScrollY,d=a<=this.offset;return c&&b||d},update:function(){var a=this.getScrollY(),b=a>this.lastKnownScrollY?"down":"up",c=this.toleranceExceeded(a,b);this.isOutOfBounds(a)||(a<=this.offset?this.top():this.notTop(),a+this.getViewportHeight()>=this.getScrollerHeight()?this.bottom():this.notBottom(),this.shouldUnpin(a,c)?this.unpin():this.shouldPin(a,c)&&this.pin(),this.lastKnownScrollY=a)}},e.options={tolerance:{up:5,down:0},offset:70,scroller:window,classes:{pinned:"headroom--pinned",unpinned:"headroom--unpinned",top:"headroom--top",notTop:"headroom--not-top",bottom:"headroom--bottom",notBottom:"headroom--not-bottom",initial:"headroom"}},e.cutsTheMustard="undefined"!=typeof f&&f.rAF&&f.bind&&f.classList,e});

/*!
 * hey, [be]Lazy.js - v1.5.4 - 2016.03.06
 * A fast, small and dependency free lazy load script (https://github.com/dinbror/blazy)
 * (c) Bjoern Klinggaard - @bklinggaard - http://dinbror.dk/blazy
 */
(function(k,f){"function"===typeof define&&define.amd?define(f):"object"===typeof exports?module.exports=f():k.Blazy=f()})(this,function(){function k(b){setTimeout(function(){var c=b._util;c.elements=w(b.options.selector);c.count=c.elements.length;c.destroyed&&(c.destroyed=!1,b.options.container&&h(b.options.container,function(a){l(a,"scroll",c.validateT)}),l(window,"resize",c.saveViewportOffsetT),l(window,"resize",c.validateT),l(window,"scroll",c.validateT));f(b)},1)}function f(b){for(var c=b._util,a=0;a<c.count;a++){var d=c.elements[a],g=d.getBoundingClientRect();if(g.right>=e.left&&g.bottom>=e.top&&g.left<=e.right&&g.top<=e.bottom||n(d,b.options.successClass))b.load(d),c.elements.splice(a,1),c.count--,a--}0===c.count&&b.destroy()}function q(b,c,a){if(!n(b,a.successClass)&&(c||a.loadInvisible||0<b.offsetWidth&&0<b.offsetHeight))if(c=b.getAttribute(p)||b.getAttribute(a.src)){c=c.split(a.separator);var d=c[r&&1<c.length?1:0],g="img"===b.nodeName.toLowerCase();g||void 0===b.src?(c=new Image,c.onerror=function(){a.error&&a.error(b,"invalid");b.className=b.className+" "+a.errorClass},c.onload=function(){g?b.src=d:b.style.backgroundImage='url("'+d+'")';t(b,a)},c.src=d):(b.src=d,t(b,a))}else a.error&&a.error(b,"missing"),n(b,a.errorClass)||(b.className=b.className+" "+a.errorClass)}function t(b,c){b.className=b.className+" "+c.successClass;c.success&&c.success(b);h(c.breakpoints,function(a){b.removeAttribute(a.src)});b.removeAttribute(c.src)}function n(b,c){return-1!==(" "+b.className+" ").indexOf(" "+c+" ")}function w(b){var c=[];b=document.querySelectorAll(b);for(var a=b.length;a--;c.unshift(b[a]));return c}function u(b){e.bottom=(window.innerHeight||document.documentElement.clientHeight)+b;e.right=(window.innerWidth||document.documentElement.clientWidth)+b}function l(b,c,a){b.attachEvent?b.attachEvent&&b.attachEvent("on"+c,a):b.addEventListener(c,a,!1)}function m(b,c,a){b.detachEvent?b.detachEvent&&b.detachEvent("on"+c,a):b.removeEventListener(c,a,!1)}function h(b,c){if(b&&c)for(var a=b.length,d=0;d<a&&!1!==c(b[d],d);d++);}function v(b,c,a){var d=0;return function(){var g=+new Date;g-d<c||(d=g,b.apply(a,arguments))}}var p,e,r;return function(b){var a=this,d=a._util={};d.elements=[];d.destroyed=!0;a.options=b||{};a.options.error=a.options.error||!1;a.options.offset=a.options.offset||100;a.options.success=a.options.success||!1;a.options.selector=a.options.selector||".b-lazy";a.options.separator=a.options.separator||"|";a.options.container=a.options.container?document.querySelectorAll(a.options.container):!1;a.options.errorClass=a.options.errorClass||"b-error";a.options.breakpoints=a.options.breakpoints||!1;a.options.loadInvisible=a.options.loadInvisible||!1;a.options.successClass=a.options.successClass||"b-loaded";a.options.validateDelay=a.options.validateDelay||25;a.options.saveViewportOffsetDelay=a.options.saveViewportOffsetDelay||50;a.options.src=p=a.options.src||"data-src";r=1<window.devicePixelRatio;e={};e.top=0-a.options.offset;e.left=0-a.options.offset;a.revalidate=function(){k(this)};a.load=function(a,b){var c=this.options;void 0===a.length?q(a,b,c):h(a,function(a){q(a,b,c)})};a.destroy=function(){var a=this._util;this.options.container&&h(this.options.container,function(b){m(b,"scroll",a.validateT)});m(window,"scroll",a.validateT);m(window,"resize",a.validateT);m(window,"resize",a.saveViewportOffsetT);a.count=0;a.elements.length=0;a.destroyed=!0};d.validateT=v(function(){f(a)},a.options.validateDelay,a);d.saveViewportOffsetT=v(function(){u(a.options.offset)},a.options.saveViewportOffsetDelay,a);u(a.options.offset);h(a.options.breakpoints,function(a){if(a.width>=window.screen.width)return p=a.src,!1});k(a)}});

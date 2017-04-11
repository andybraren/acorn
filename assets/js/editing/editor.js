window.addEventListener('load', function() {
    var editor;
});

// https://www.sitepoint.com/delay-sleep-pause-wait/
function wait(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}



// Initialize the editor
editor = ContentTools.EditorApp.get();
editor.init('*[data-editable]', 'data-name');

editor.addEventListener('saved', function (ev) {
    var name, onStateChange, passive, payload, regions, xhr;

    // Check if this was a passive save
    passive = ev.detail().passive;

    // Check to see if there are any changes to save
    regions = ev.detail().regions;
    if (Object.keys(regions).length == 0) {
        return;
    }

    // Set the editor's state to busy while we save our changes
    this.busy(true);

    // Collect the contents of each region into a FormData instance
    payload = new FormData();
    payload.append('__page__', window.location.pathname);
    payload.append('page', window.location.pathname);
    for (name in regions) {
        if (regions.hasOwnProperty(name)) {
            //payload.append(name, regions[name]);
            //payload.append(name, toMarkdown(regions[name], { converters: kirbytagtweaks }));
        }
    }

    // Send the update content to the server to be saved
    function onStateChange(ev) {
        // Check if the request is finished
        if (ev.target.readyState == 4) {
            editor.busy(false);
            if (ev.target.status == '200') {
                // Save was successful, notify the user with a flash
                if (!passive) {
                    new ContentTools.FlashUI('ok');
                    
                  // Andy - redirect to the destination URL that the server responded with
                  // var data = JSON.parse(this.response);
                  data = JSON.parse(this.response);
                  //console.log(data.redirecturl);
                  redirecturl = data.redirecturl;
                  wait(3000);
                  window.location.href = redirecturl;
                }
            } else {
                // Save failed, notify the user with a flash
                new ContentTools.FlashUI('no');
            }
        }
    };

    xhr = new XMLHttpRequest();
    xhr.addEventListener('readystatechange', onStateChange);
    xhr.open('POST', 'save');
    //xhr.send(payload);
    
    
    //console.log('response=',xhr.responseXML);
    if (xhr.responseXML == null) {
      
    }

});








// Kirby-specific Markdown extensions for to-markdown
// Overriding isn't currently possible, so some filters were removed from to-markdown.js and placed here instead
// https://github.com/domchristie/to-markdown/issues/103
// 
// This is used to convert certain HTML elements that ContentTools pushes into Kirbytags

String.prototype.str_replace = function(search, replace) {
    return this.split(search).join(replace);
}

var kirbytagtweaks = [
  {
    filter: 'h1',
    replacement: function(content, node) {
      return content;
    }
  },
  
  { // Use - instead of * for lists
    filter: 'li',
    replacement: function (content, node) {
      content = content.replace(/^\s+/, '').replace(/\n/gm, '\n    ')
      var prefix = '- '
      var parent = node.parentNode
      var index = Array.prototype.indexOf.call(parent.children, node) + 1

      prefix = /ol/i.test(parent.nodeName) ? index + '.  ' : '- '
      return prefix + content
    }
  },
  
  {
    filter: 'figure', // could be either img or HTML5 <video>
    replacement: function(content, node) {
      
      if (node.childNodes[0].href != null) { // it's an image
        var filename = node.childNodes[0].getAttribute('href').split("/").pop().replace(/\%20/g,' ') || '';
        //var filename = str_replace('%20', ' ', blahfilename);
        var figcaption = node.childNodes[1];
        if (figcaption) {
          var caption = ' caption: ' + trim(figcaption.textContent) || '';
        } else {
          var caption = '';
        }
        return filename ? '(image: ' + filename + caption + ')' : '';  
      }
      
      else if (node.childNodes[0].nodeName == 'VIDEO') {
        
        var file = node.childNodes[0].getAttribute('data-file') || '';
        var size = (node.getAttribute('class')) ? ' size: ' + node.getAttribute('class') : '';
        var autoplay = (node.childNodes[0].hasAttribute('autoplay')) ? ' autoplay: on' : '';
        var caption = (node.childNodes[2]) ? ' caption: ' + node.childNodes[2].textContent : '';
        
        return file ? '(video: ' + file + size + autoplay + caption + ')' : '';
      }
      
      else if (node.childNodes[0].childNodes[1].href != null) { // it's a guggenheim image
        var filename = node.childNodes[0].childNodes[1].getAttribute('data-share-src').split("/").pop() || '';
        return filename ? filename + ', ' : '';
      }
      
      else {
        //var videourl = node.childNodes[0].childNodes[1];
      }
      
    }
  },
  
  {
    filter: 'img',
    replacement: function(content, node) {
      var alt = node.alt || '';
      var src = node.getAttribute('src') || '';
      
      if (node.getAttribute('class')) {
        if (node.getAttribute('class').includes('align-right')) {
          var kirbysize = 'medium';
        }
        if (node.getAttribute('class').includes('align-left')) {
          var kirbysize = 'medium';
        }
        if (node.getAttribute('class').includes('medium')) {
          var kirbysize = 'medium';
        }
        if (node.getAttribute('class').includes('small')) {
          var kirbysize = 'small';
        }
      }
      
      var size = node.getAttribute('class') ? ' size: ' + kirbysize : '';
      var caption = node.getAttribute('data-caption') ? ' caption: ' + node.getAttribute('data-caption') : '';
      
      var title = node.title || '';
      var titlePart = title ? ' "'+ title +'"' : '';
      
      //var blah2 = src.split("/").pop() || '';
      //var filename = str_replace('%20', ' ', blah2);
      
      if (node.getAttribute('alt') == 'Image') {
        //var filename = node.src.split("/").pop().replace(/\%20/g,' ');
        var filename = node.src.split("/").pop().replace(/\%20/g,' ').replace(/(\-.*)\./, '.');
      } else {
        var filename = node.getAttribute('data-filename');
      }
      
      console.log(filename);
      
      
      //return src ? '(image: ' + src.split("/").pop().replace(/-(\d+)x(\d+)\./g,'.') + size + caption + ')' : '';
      //return src ? '(image: ' + filename + size + caption + ')' : '';
      //var filename = node.parentNode.getAttribute('href').split("/").pop().replace(/\%20/g,' ') || '';
      
      return src ? '(image: ' + filename + size + caption + ')' : '';
    }
  },
  /*
  { // Images uploaded via ContentTools
    filter: function (node) {
      return node.nodeName === 'DIV' && node.classList.contains('ce-element--type-image');
    },
    replacement: function(content, node) {
      
      var url = node.getAttribute('style').match(/url\(([^)]+)\)/i);
      var url = url.toString();
      //console.log(url);
      
      var filename = url.split('/').pop();
      
      console.log(filename);
      
      return '(image: ' + filename + ')';
      
    }
  },
  */
  
  { // Files
    filter: function (node) {
      return node.nodeName === 'A' && node.classList.toString().includes('file-');
    },
    replacement: function(content, node) {      
      //var filename = node.href.split('/').pop().replace(/\%20/g,' ');
      //console.log(filename);
      var filename = node.getAttribute('data-filename');
      var text = node.innerHTML;
      
      if (filename != text) {
        return '(file: ' + filename + ' text: ' + text + ')';
      } else {
        return '(file: ' + filename + ')';
      }
      
    }
  },
  
  {
    // <iframe frameborder="0" height="300" src="https://www.youtube.com/embed/-xzzQVk5IfE" width="400"></iframe>
    // (video: https://www.youtube.com/embed/-xzzQVk5IfE)
    // src\s*=\s*"(.+?)"
    filter: 'iframe',
    replacement: function(content, node) {
      var src = node.getAttribute('src') || '';
      return src ? '(video: ' + src + ')' : '';
    }
  },

  {
    // Video embeds from ContentTools, wrapped in a video-container
    filter: function (node) {
      return node.nodeName === 'DIV' && node.getAttribute('class') == 'video-container';
    },
    replacement: function(content, node) {
      var src = node.childNodes[0].getAttribute('src') || '';
      return src ? '(video: ' + src + ')' : '';
    }
  },
  
  { // Galleries from Guggenheim, wrapped in "guggenheim" div
    filter: function (node) {
      return node.nodeName === 'DIV' && node.classList.contains('guggenheim');
    },
    replacement: function(content, node) {
      
      var filenames = '';
      var images = node.getElementsByTagName('a');
      
      for(var i = 0; i < images.length; i++) {
        var url = images[i].getAttribute('data-share-src');
        var filenames = filenames + url.substring(url.lastIndexOf('/')+1) + ', ';
      }
      
      var filenames = filenames.slice(0, -2);
      
      return '(gallery: ' + filenames + ')';
      
    }
  },
];


/*
Need a script that converts this:
<figure style="padding-top:62.571428571429%;"><a href="https://drewbaren.com/maker/content/frontend-editing-example/1duck.jpg" data-size="1920x1200"><img src="https://drewbaren.com/maker/thumbs/frontend-editing-example/1duck-700x438.jpg" alt="1duck" class="b-lazy b-loaded"><noscript><img src="https://drewbaren.com/maker/thumbs/frontend-editing-example/1duck-700x438.jpg" alt="1duck" class="b-lazy b-loaded"></noscript></a><figcaption>blahdeblah</figcaption></figure>

Into this:
<img src="https://drewbaren.com/maker/content/frontend-editing-example/1duck.jpg" width="740" height="520">

Right before the editor is initated. This will allow ContentEditable to manipulate the image normally, and do whatever it normally does with it.

However, captions are an issue. I'm thinking the best option would be to use JS to forcibly add a span that sits below the <img> and looks like a figcaption, and follow the image around wherever it moves. The span will be editable with contenteditable, and the JS will auto-update whatever is inside the span with a data-src attribute in the nearby image.

When they hit the edit button, to-markdown will grab the data-caption value and use that as the caption when creating the kirbytext image

*/

function replaceFigures() {
  var figures = [].slice.call(document.getElementsByTagName('article')[0].getElementsByTagName('figure'));
  if (figures != null) {
    for (var i = 0; i < figures.length; i++) {
      
      // For figures that contain external video embeds
      if (figures[i].childNodes[0].classList.contains('video-container')) {
        
        if (figures[i].childNodes[0].childNodes[0].classList.contains('youtube') || figures[i].childNodes[0].childNodes[0].classList.contains('vimeo')) {
          var newiFrame = document.createElement('iframe');
          newiFrame.src = figures[i].childNodes[0].childNodes[1].getAttribute('data-orig');
          newiFrame.setAttribute('style', figures[i].childNodes[0].childNodes[0].getAttribute('style'));
          newiFrame.setAttribute('class', figures[i].childNodes[0].childNodes[0].getAttribute('class'));
          
          //console.log.newiFrame;
          
          figures[i].parentNode.replaceChild(newiFrame,figures[i]);
        }
      }
      
      else if (figures[i].childNodes[0].nodeName == 'VIDEO') {
        //console.log('yes');
      }
      
      else if (figures[i].parentElement.classList.contains('guggenheim__row')) {
        //console.log('yes');
      }
      
      else {
                
        var newImg = document.createElement('img');
        
        if (figures[i].getAttribute('class') != null) {
          newImg.setAttribute('class', figures[i].getAttribute('class'));
        }
        
        if (figures[i].childNodes[0].getAttribute('href') != null) {
          newImg.setAttribute('data-filename',figures[i].childNodes[0].getAttribute('href').split("/").pop().replace(/\%20/g,' '));
        }
        
        if (figures[i].childNodes[1]) {
          var imgcaption = figures[i].childNodes[1].textContent;
          newImg.setAttribute('data-caption',imgcaption);
  
          //var datacaption = document.createAttribute('data-caption');
          //datacaption.nodeValue = imgcaption;
          //newImg.setAttributeNode(datacaption);
          //figures[i].childNodes[0].setAttributeNode(datacaption);
        }
        
        // newImg.src = figures[i].childNodes[0].href;
        
        var datasrc = figures[i].childNodes[0].childNodes[0].getAttribute('data-src');
        if (datasrc) {
          newImg.src = datasrc; // try getting data-src first, if it still exists
        } else {
          newImg.src = figures[i].childNodes[0].childNodes[0].src;
        }
        
        
        // These get the actual file size, which isn't what we want
        //newImg.width = figures[i].childNodes[0].getAttribute('data-size').split('x')[0];
        //newImg.height = figures[i].childNodes[0].getAttribute('data-size').split('x')[1];
        
        // Add width and height attributes for ContentTools' sake https://github.com/GetmeUK/ContentTools/issues/218
        var imgSize = figures[i].childNodes[0].childNodes[0].getAttribute('data-size');
        if (imgSize) {
          newImg.width = figures[i].childNodes[0].childNodes[0].getAttribute('data-size').split('x')[0];
          newImg.height = figures[i].childNodes[0].childNodes[0].getAttribute('data-size').split('x')[1];
        }
        
        //console.log(newImg);
  
        // figures[i].childNodes[0].href = figures[i].childNodes[0].childNodes[0].src; // doesn't work down here for some reason
        
        figures[i].parentNode.replaceChild(newImg,figures[i]);
      }

    }
  }
}

// This appears to run after ContentTools already has. 
function addTempCaptions() {
  var imgdivs = document.getElementsByClassName('ce-element--type-image');
  if (imgdivs != null) {
    for (var i = 0; i < imgdivs.length; i++) {
      
      var datacaption2 = document.createAttribute('data-caption');
      datacaption2.nodeValue = 'heyy';
      imgdivs[i].setAttributeNode(datacaption2);
      
      //console.log(imgdivs[i]);
      
    }
  }
}

// Can't figure out how to make data-caption restricted from ContentTools
// https://github.com/GetmeUK/ContentTools/pull/137


/* Create a new <p> when an image is selected and the user hits Enter
  - https://github.com/GetmeUK/ContentTools/issues/91
  - currently adds extra <br> for some reason, need to tweak

*/
/*
document.addEventListener('keypress', function(ev) {
    // Was the enter key pressed
    if (ev.keyCode != 13) {
        return;
    }

    // Is the selected element an image
    selectedElm = ContentEdit.Root.get().focused();
    if (selectedElm.type() === 'Image' || selectedElm.type() === 'Video') {
        // Attach a new paragraph to the parent after the image
        p = new ContentEdit.Text('p', {}, '')
        selectedElm.parent().attach(p, selectedElm.parent().children.indexOf(selectedElm) + 1)

        // Move the focus and selection to the new paragraph
        p.focus()
        selection = new ContentSelect.Range(0, 0)
        selection.select(p.domElement())
    }
    
});
*/

// ContentTools tweaks
// https://github.com/GetmeUK/ContentTools/issues/154

var __hasProp = {}.hasOwnProperty;
var __extends = function(child, parent) {
    for (var key in parent) {
        if (__hasProp.call(parent, key)) 
            child[key] = parent[key];
    }

    function ctor() { 
        this.constructor = child; 
    }

    ctor.prototype = parent.prototype;

    child.prototype = new ctor();
    child.__super__ = parent.prototype; 

    return child; 
};

ContentTools.Tools.Heading2 = (function(_super) {
    __extends(Heading2, _super);

    // This class extends the existing Bold tool    
    function Heading2() {
      return Heading2.__super__.constructor.apply(this, arguments);
    }

    // Stow the tool so we can reference it later using 'heading2'
    ContentTools.ToolShelf.stow(Heading2, 'heading2');

    // Set the tool tip that will appear
    Heading2.label = 'Heading';

    // Set the name of the icon (this wont exist unless you add one)
    Heading2.icon = 'heading';

    // Set the tag that will be used to wrap content when pressing this tool
    Heading2.tagName = 'h2';

    Heading2.canApply = function(element, selection) {
      if (element.isFixed()) {
        return false;
      }
      return element.content !== void 0 && ['Text', 'PreText'].indexOf(element.type()) !== -1;
    };

    Heading2.isApplied = function(element, selection) {
      if (!element.content) {
        return false;
      }
      if (['Text', 'PreText'].indexOf(element.type()) === -1) {
        return false;
      }
      return element.tagName() === this.tagName;
    };

    Heading2.apply = function(element, selection, callback) {
      var content, insertAt, parent, textElement;
      element.storeState();
      if (element.type() === 'PreText') {
        content = element.content.html().replace(/&nbsp;/g, ' ');
        textElement = new ContentEdit.Text(this.tagName, {}, content);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(textElement, insertAt);
        element.blur();
        textElement.focus();
        textElement.selection(selection);
      } else {
        element.attr('class', '');
        if (element.tagName() === this.tagName) {
          element.tagName('p');
        } else {
          element.tagName(this.tagName);
        }
        element.restoreState();
      }
      return callback(true);
    };

    return Heading2;

})(ContentTools.Tool);

// Add the new `heading2` tool in it's own group to the toolbox
//ContentTools.DEFAULT_TOOLS.unshift(['heading2']);

ContentTools.Tools.Heading3 = (function(_super) {
    __extends(Heading3, _super);

    // This class extends the existing Bold tool    
    function Heading3() {
      return Heading3.__super__.constructor.apply(this, arguments);
    }

    // Stow the tool so we can reference it later using 'heading3'
    ContentTools.ToolShelf.stow(Heading3, 'heading3');

    // Set the tool tip that will appear
    Heading3.label = 'Subheading';

    // Set the name of the icon (this wont exist unless you add one)
    Heading3.icon = 'subheading';

    // Set the tag that will be used to wrap content when pressing this tool
    Heading3.tagName = 'h3';

    Heading3.canApply = function(element, selection) {
      if (element.isFixed()) {
        return false;
      }
      return element.content !== void 0 && ['Text', 'PreText'].indexOf(element.type()) !== -1;
    };

    Heading3.isApplied = function(element, selection) {
      if (!element.content) {
        return false;
      }
      if (['Text', 'PreText'].indexOf(element.type()) === -1) {
        return false;
      }
      return element.tagName() === this.tagName;
    };

    Heading3.apply = function(element, selection, callback) {
      var content, insertAt, parent, textElement;
      element.storeState();
      if (element.type() === 'PreText') {
        content = element.content.html().replace(/&nbsp;/g, ' ');
        textElement = new ContentEdit.Text(this.tagName, {}, content);
        parent = element.parent();
        insertAt = parent.children.indexOf(element);
        parent.detach(element);
        parent.attach(textElement, insertAt);
        element.blur();
        textElement.focus();
        textElement.selection(selection);
      } else {
        element.attr('class', '');
        if (element.tagName() === this.tagName) {
          element.tagName('p');
        } else {
          element.tagName(this.tagName);
        }
        element.restoreState();
      }
      return callback(true);
    };

    return Heading3;

})(ContentTools.Tool);

// Patch the behaviour of the Video `html` method to wrap the iframe output
ContentEdit.Video.prototype.html = function (indent) {
    var indent;
    if (indent == null) {
        indent = '';
    }

  // Return a HTML string for the node
    if (this.tagName() == 'video') {
        return ContentEdit.Video.__super__.html.call(this, indent);
    } else {
        // Wrap the returned string in a div tag
        return indent + '<div class="video-container">' +
            "<" + this._tagName + this._attributesToString() + ">" +
            "</" + this._tagName + ">" +
            "</div>";
    }
}

// Patch the behaviour of the Video `html` method to cater for a wrapped video
var _innerFromDOMElement = ContentEdit.Video.fromDOMElement;
ContentEdit.Video.fromDOMElement = function (domElement) {
    var iframe = domElement.querySelector('iframe');
    if (iframe) {
        return _innerFromDOMElement.call(this, iframe);
    } else {
        return _innerFromDOMElement.call(this, domElement);
    }
}

// Add the new `heading3` tool in it's own group to the toolbox
// ContentTools.DEFAULT_TOOLS.unshift(['heading3']);

// editor.toolbox().tools([['bold', 'italic', 'link', 'align-left', 'align-center', 'align-right'], ['heading', 'subheading', 'paragraph', 'unordered-list', 'ordered-list', 'table', 'indent', 'unindent', 'line-break'], ['image', 'video', 'preformatted'], ['undo', 'redo', 'remove']]);

// Revise the layout of tools
// https://github.com/GetmeUK/ContentTools/issues/173
editor.toolbox().tools([['bold', 'italic', 'link', 'heading2', 'heading3', 'paragraph', 'unordered-list', 'ordered-list', 'table'], ['image', 'video'], ['undo', 'redo', 'remove']]);





// Image Upload
ContentTools.IMAGE_UPLOADER = imageUploader;

function imageUploader(dialog) {
    var image, xhr, xhrComplete, xhrProgress;
    
    dialog.addEventListener('imageuploader.cancelupload', function () {
        // Cancel the current upload

        // Stop the upload
        if (xhr) {
            xhr.upload.removeEventListener('progress', xhrProgress);
            xhr.removeEventListener('readystatechange', xhrComplete);
            xhr.abort();
        }

        // Set the dialog to empty
        dialog.state('empty');
    });
    
    dialog.addEventListener('imageuploader.clear', function () {
        // Clear the current image
        dialog.clear();
        image = null;
    });
    
    dialog.addEventListener('imageuploader.fileready', function (ev) {

        // Upload a file to the server
        var formData;
        var file = ev.detail().file;

        // Define functions to handle upload progress and completion
        xhrProgress = function (ev) {
            // Set the progress for the upload
            dialog.progress((ev.loaded / ev.total) * 100);
        }

        xhrComplete = function (ev) {
            var response;

            // Check the request is complete
            if (ev.target.readyState != 4) {
                return;
            }

            // Clear the request
            xhr = null
            xhrProgress = null
            xhrComplete = null

            // Handle the result of the upload
            if (parseInt(ev.target.status) == 200) {
                // Unpack the response (from JSON)
                response = JSON.parse(ev.target.responseText);

                // Store the image details
                image = {
                    size: response.size,
                    url: response.url
                    };

                // Populate the dialog
                dialog.populate(image.url, image.size);

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        }

        // Set the dialog state to uploading and reset the progress bar to 0
        dialog.state('uploading');
        dialog.progress(0);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('image', file);
        
        formData.append('page', window.location.pathname);

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', xhrProgress);
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', window.location.href  + '/upload', true);
        xhr.send(formData);
    });

    dialog.addEventListener('imageuploader.save', function () {
        var crop, cropRegion, formData;

        // Define a function to handle the request completion
        xhrComplete = function (ev) {
            // Check the request is complete
            if (ev.target.readyState !== 4) {
                return;
            }

            // Clear the request
            xhr = null
            xhrComplete = null

            // Free the dialog from its busy state
            dialog.busy(false);

            // Handle the result of the rotation
            if (parseInt(ev.target.status) === 200) {
                // Unpack the response (from JSON)
                var response = JSON.parse(ev.target.responseText);

                // Trigger the save event against the dialog with details of the
                // image to be inserted.
                dialog.save(
                    response.url,
                    response.size,
                    {
                        'alt': response.alt,
                        'data-ce-max-width': image.size[0]
                    });

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        }

        // Set the dialog to busy while the rotate is performed
        dialog.busy(true);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('url', image.url);

        // Set the width of the image when it's inserted, this is a default
        // the user will be able to resize the image afterwards.
        formData.append('width', 700);

        // Check if a crop region has been defined by the user
        if (dialog.cropRegion()) {
            formData.append('crop', dialog.cropRegion());
        }

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', window.location.href  + '/insert-image', true);
        xhr.send(formData);
    });

}




























// myRIO WebSocket Communicator
// Based on http://blog.teamtreehouse.com/an-introduction-to-websockets
// Connects to the 'Taco' myRIO and sends/receives textual data

var message = "";

function connect() {

  // Config
  var port = 6123;
  var host = "ws://"+document.getElementById('ipaddress').value+":"+port;
  // console.log(host);
  // var host = "ws://130.64.149.241:"+port;
  // ws://172.22.11.2:6123   // Jason USB
  // ws://130.64.94.197:6123 // tuftswireless TACO
  // 130.64.149.241

  // Declare Variables
  var socket;
  var explodedValues = [0,0,0,0]; //initial value for the plot = 0




  // Attempt to select only the clicked widget
  var closest = function closest(el, fn) {
      return el && ( fn(el) ? el : closest(el.parentNode, fn) );
  };

  console.log('Clicked id is:' + this.id);
  var srcEl = this.id;
  console.log('Clicked element is:' + srcEl);

  var srcEl = document.getElementsByClassName('connectBtn');
  console.log('The element is:' + srcEl);

  var websocketparent = closest(srcEl[0], function(el) {
      return el.className === 'websocket';
  });

  
  console.log("Testing");
  console.log(websocketparent);




  // Get references to elements on the page
  var form = document.getElementById('message-form');
  var messageField = document.getElementById('message');
  var messagesList = document.getElementById('messages');
  var socketStatus = document.getElementById('status');
  var socketStatusColor = document.getElementById('websocket');
  
  var LED1 = document.getElementById('LED1');
  var LED2 = document.getElementById('LED2');
  var LED3 = document.getElementById('LED3');
  var LED4 = document.getElementById('LED4');

  var websocketpanel = document.getElementById('websocket-panel');

  var connectBtn = document.getElementById('connectBtn');
  var closeBtn = document.getElementById('closeBtn');
  var reconnectBtn = document.getElementById('reconnectBtn');
  var websocketstatus = document.getElementById('websocket');



  // find nearest parent element
  /*
  var closest = function closest(el, fn) {
      return el && ( fn(el) ? el : closest(el.parentNode, fn) );
  };
    var clickedListItem = closest(eTarget, function(el) {
      return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
    });
  */

  // Create a new WebSocket
  var socket = new WebSocket(host);
  //console.log('WebSocket status '+socket.readyState);
  //console.log("Welcome - status "+this.readyState);

  // Show a connected message when the WebSocket is opened.
  socket.onopen = function(event) {

    websocketstatus.className = 'websocket status-green';

    websocketpanel.className = "show"

    reconnectBtn.className = 'size-50 hide';
    connectBtn.className = 'size-50 hide';
    closeBtn.className = 'size-50 show';

    socketStatus.innerHTML = 'Connected';
    socketStatus.className = 'open';
    socketStatusColor.className = 'websocket status-green';
  };

  // Handle any errors that occur.
  socket.onerror = function(error) {
    console.log('WebSocket Error: ' + error);
  };

  // Send a message when the form is submitted.
  form.onsubmit = function(e) {
    e.preventDefault();

    // Retrieve the message from the textarea.
    var message = messageField.value;

    // Send the message through the WebSocket.
    socket.send(message);

    // Add the message to the messages list.
    messagesList.innerHTML += '<li class="sent"><span>Sent:</span>' + message + '</li>';

    // Clear out the message field.
    messageField.value = '';

    return false;
  };

  // Handle messages sent by the server.
  socket.onmessage = function(event) {
    message = event.data;
    //console.log("Message Received: "+message);
    messagesList.innerHTML += '<li class="received"><span>Received:</span>' + message + '</li>';
    if (message == 'LED1on') {LED1.style.backgroundColor = "#38EA38";}
    if (message == 'LED2on') {LED2.style.backgroundColor = "#38EA38";}
    if (message == 'LED3on') {LED3.style.backgroundColor = "#38EA38";}
    if (message == 'LED4on') {LED4.style.backgroundColor = "#38EA38";}
    if (message == 'LED1off') {LED1.style.backgroundColor = "#D8D8D8";}
    if (message == 'LED2off') {LED2.style.backgroundColor = "#D8D8D8";}
    if (message == 'LED3off') {LED3.style.backgroundColor = "#D8D8D8";}
    if (message == 'LED4off') {LED4.style.backgroundColor = "#D8D8D8";}

  google.load('visualization', '1', {packages: ['corechart'], callback: drawVisualization});
  };

  // Show a disconnected message when the WebSocket is closed.
  socket.onclose = function(event) {
    console.log("Disconnected - status "+this.readyState);

    socketStatusColor.className = 'websocket status-red';
    closeBtn.className = 'size-50 hide';
    reconnectBtn.className = 'size-50 hide';

    socketStatus.innerHTML = 'WebSocket Closed.';
    socketStatus.className = 'closed';
  };

  // Close the WebSocket connection when the close button is clicked.
  closeBtn.onclick = function(e) {
    e.preventDefault();

    // Close the WebSocket.
    socket.close();

    return false;
  };

};

function duplicate(ip) {
  var elements = document.getElementsByClassName('ipaddress');
  for (var i = 0; i < elements.length; i++) {
    elements[i].value = ip;
  }
}

function quit(){
  if (socket != null) {
    console.log("Close Socket");
    socket.close();
    socket=null;
  }
}

function reconnect() {
  quit();
  connect();
}

function drawVisualization(event) {
  //console.log("hello");
  //console.log(message);
  var explodedValuesString = message.split(';');
  console.log(explodedValuesString);
  var explodedValues = JSON.parse(explodedValuesString);
  console.log(explodedValues);

  // Create and populate the data table from the values received via websocket
  var data = google.visualization.arrayToDataTable([
    ['Tracker', '1'],
    ['X', explodedValues['X-Axis']],
    ['Y', explodedValues['Y-Axis']],
    ['Z', explodedValues['Z-Axis']]
  ]);
  
  // use a DataView to 0-out all the values in the data set for the initial draw
  var view = new google.visualization.DataView(data);
  view.setColumns([0, {
    type: 'number',
    label: data.getColumnLabel(1),
    calc: function () {return 0;}
  }]);
  
  // Create and draw the plot
  var chart = new google.visualization.LineChart(document.getElementById('visualization'));
  
  var options = {
      title:"myRIO Data Output",
      height: 400,
      bar: { groupWidth: "95%" },
      legend: { position: "none" },
      animation: {
      duration: 1,
      easing: 'in'
    },
    hAxis: {
      // set these values to make the initial animation smoother
      minValue: 0,
      maxValue: 10
    },
    vAxis: {
      viewWindowMode: 'explicit',
      viewWindow: {
        max: 2,
        min: -2,
      },
      gridlines: {
        count: 10,
      }
    }
  };
  
  var runOnce = google.visualization.events.addListener(chart, 'ready', function () {
      google.visualization.events.removeListener(runOnce);
      chart.draw(data, options);
  });
  
  chart.draw(view, options);
  
  // you can handle the resizing here - no need to recreate your data and charts from scratch
  $(window).resize(function() {
      chart.draw(data, options);
  });
}






// Make WebSocket data box stick to the bottom unless scrolled up
// http://stackoverflow.com/questions/18614301/keep-overflow-div-scrolled-to-bottom-unless-user-scrolls-up
// Doesn't quite work
/*
var out = document.getElementById("messages");
var add = setInterval(function() {
    // allow 1px inaccuracy by adding 1
    var isScrolledToBottom = out.scrollHeight - out.clientHeight <= out.scrollTop + 1;
    // scroll to bottom if isScrolledToBotto
    if(isScrolledToBottom) {
      out.scrollTop = out.scrollHeight - out.clientHeight;
    }
}, 1000);
*/

// This should work without the interval if I can force the div to have 1px of scrollabel region
var add = setInterval(function() {
  var objDiv = document.getElementById("messages");
  objDiv.scrollTop = objDiv.scrollHeight;
}, 1);







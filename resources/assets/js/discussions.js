document.addEventListener('turbolinks:load', function(e) {
	discussionsHandler();
});

var lastTypingUpdate = null;
var userIsTyping = false;

function discussionsHandler() {
	let discussions = document.querySelectorAll('.node.discussion');
	for (var i = discussions.length - 1; i >= 0; i--) {
		let discussion = discussions[i];
		let node = discussion.getAttribute('data-node');
		sendIsTyping(node, false); // initialize typing state
		discussion.querySelector('form textarea').addEventListener('keyup', function(e) {
			sendIsTyping(node, true);
		});
		let form = discussion.querySelector('form');
		form.addEventListener('submit', function(e) {
			e.preventDefault();
			postMessage(node);
			form.reset();
		});
		window.setInterval(function() {
			updateAndRequestMessages(node);
		}, 1000);
	}
}

// Update user typing status, throttle to once every 30 seconds or immediately on typing state change.
function sendIsTyping(node, typing) {
	if(typing != userIsTyping || lastTypingUpdate === null || Date.now() - lastTypingUpdate >= 30000) {
		var request = new XMLHttpRequest();
		request.open('POST', '/discussion/'+node+'/typing', true);
		request.responseType = "json";
		request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').content);
		var formData = new FormData();
		formData.append('isTyping', typing);
		request.send(formData);
		lastTypingUpdate = Date.now();
	}
	userIsTyping = typing;
}

function postMessage(node) {
	var request = new XMLHttpRequest();
	request.open('POST', '/discussion/'+node+'/post', true);
	request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').content);
	var formData = new FormData(document.querySelector('#node-' + node + ' form'));
	request.send(formData);
}

//Request and append messages, update typing state if user hasn't typed for the threshold value
function updateAndRequestMessages(node) {
	// Update typing indicator if necessary
	if(userIsTyping && (Date.now() - lastTypingUpdate >= 30000)) { 
		sendIsTyping(node, false); 
	}
	var request = new XMLHttpRequest();
	request.open('GET', '/discussion/'+node+'/messages/', true);
	request.responseType = 'json';
	request.onload = function() {
	  if (this.status >= 200 && this.status < 400) {
	    // Success!
	    var data = this.response;
	    if(typeof(data) === 'string') {
	    	data = JSON.parse(data);
	    }
	    updateMessages(data.messages, node);
	    updateStatus(data.status, node);
	  } else {
	    // We reached our target server, but it returned an error

	  }
	};

	request.onerror = function() {
	  // There was a connection error of some sort
	};

	request.send();
}

function updateMessages(data, node) {
	let discussionNode = document.getElementById('node-' + node);
	if(!discussionNode) { return; }
	let messagesContainer = discussionNode.querySelector('.messagelist .scroll');
	if(!messagesContainer) { return; }
	let nodeList = createElementFromHTML(data);
	let nodes = nodeList.querySelectorAll('div');
	//Check if scroll position of messagelist is at bottom
 	var isAtBottom = false;
	if(Math.abs(messagesContainer.scrollHeight - messagesContainer.getBoundingClientRect().height - messagesContainer.scrollTop) <= 1) {
		isAtBottom = true;
	}
	for (var i = nodes.length - 1; i >= 0; i--) {
		let node = nodes[i];
		let oldMessage = messagesContainer.querySelector('[data-id="' + node.getAttribute('data-id') + '"]');
		if(!oldMessage) {
			messagesContainer.appendChild(node);
		} else {
			oldMessage.querySelector('time').innerHTML = node.querySelector('time').innerHTML;
		}
	}
	if(isAtBottom) { messagesContainer.scrollTop = messagesContainer.scrollHeight; }
}

function createElementFromHTML(htmlString) {
  var div = document.createElement('div');
  div.innerHTML = htmlString.trim();
  return div; 
}

function updateStatus(status, node) {
	let discussionNode = document.getElementById('node-' + node);
	if(!discussionNode) { return; }
	let lowerTyping = (userIsTyping === true) ? 1 : 0;
	status.typing = Math.max(0, status.typing - lowerTyping); // Don't count this user as one typing.
	let statusNode = discussionNode.querySelector('.discussionstatus');
	let personsTyping = (status.typing >= 2) ? statusNode.dataset.langPersons : statusNode.dataset.langPerson;
	let personsPresent = (status.present >= 2 || status.present == 0) ? statusNode.dataset.langPersons : statusNode.dataset.langPerson;
	var statusString = (status.typing !== 0) ? status.typing + " " + personsTyping + " " + statusNode.dataset.langTyping + ', ' : '';
	statusString += (status.present !== 0) ? status.present + " " + personsPresent + " " + statusNode.dataset.langPresent : '.';
	statusNode.innerHTML = statusString;


}
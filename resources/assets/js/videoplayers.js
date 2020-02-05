document.addEventListener('turbolinks:load', function(e) {
	setupPlayers();
});

	var players = [];
	function setupPlayers() {
		let containerdivs = document.querySelectorAll('.mediaplayercontainer');
		for (var i = containerdivs.length - 1; i >= 0; i--) {
			var playerdiv = containerdivs[i].querySelector('.player');
			var playerid = playerdiv.id;

			var player = new Mediasite.Player(playerid, 
			    {
			        url: playerdiv.dataset.src,
			        events: {
			            'ready': function () { 
			            	onMediaplayerReady(); 
			            },
			            'error': function (errorData) {
			                   console.log('Error: ' 
			                       + Mediasite.ErrorDescription[errorData.errorCode] 
			                       + (errorData.details ? ' (' + errorData.details + ')' : '')); 
			            },
			            'playstatechanged': onMediaplayerPlayStateChanged,
			            'timedeventreached': onMediaPlayerTimedEvent
			        }
			    }
			);
			players.push(
				{
					element: containerdivs[i],
					player: player
				}
			);
		}
	}

	function onMediaplayerPlayStateChanged(playState) { 
		console.log('changed state' + playState);
	}

	function onMediaplayerReady() {
		for (var i = players.length - 1; i >= 0; i--) {
			var player = players[i].player;
			var element = players[i].element;
			var blocks = element.querySelectorAll('.video-overlay');
			for (var j = blocks.length - 1; j >= 0; j--) {
				player.addTimedEvent(Number(blocks[j].dataset.timestamp), 'showBlock', {id: blocks[j].dataset.id, playerIndex: i});
			}
		}
	}

	function onMediaPlayerTimedEvent(event) {
		var payload = event.timedEventPayload;
		var player = players[payload.playerIndex].player;
		showBlock(event.timedEventPayload.id, player);
		player.pause();
	}

	function showBlock(id, player) {
		var block = document.getElementById('video-block-' + id);
		var playButton = block.querySelector('button.play');
		playButton.addEventListener('click', function(e) {
			player.play();
			block.classList.add('hidden');
		});
		block.classList.remove('hidden');
		block.addEventListener('submit', function(e) {
			e.preventDefault();
			// Form can be the question form or the reset form (assumes only one is shown at a time)
			form = e.currentTarget.querySelector('form');
			postQuestion(id);
		});
	}

	function postQuestion(id) {
		var block = document.getElementById('video-block-' + id);
		var form = block.querySelector('form');
		var request = new XMLHttpRequest();
		request.overrideMimeType("application/json");
		request.open('POST', form.action, true);
		request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').content);
		request.onload = function() {
		  if (this.status >= 200 && this.status < 400) {
		    // Success!
		    
		    var data = JSON.parse(this.response);
		    var replaceNode = block.querySelector('.replacenode');
		    replaceNode.textContent = '';
		    replaceNode.insertAdjacentHTML('afterbegin', data.content);
		  } else {
		    // We reached our target server, but it returned an error
		    console.log('error');
		  }
		};

		request.onerror = function() {
		  console.log('connection error');
		};
		var data = new FormData(form);
		data.append('returnJson', true);
		request.send(data);
	}
document.addEventListener('turbolinks:load', function(e) {
	setupAsides();
});

let marginBottom = 16;
let marginTop = 0;

var asideList = [];
var asides = [];

var asideIsOpeningInSlot = false;

function setupAsides() {
	asides = document.querySelectorAll('[data-type=aside-block]');
	let content = document.getElementById('content');

	// Don't execute in edit mode
	let body = document.getElementsByTagName('body');
	if(!body[0].classList.contains('isediting')) {
		// resizeAsideContainers();
		window.addEventListener('resize', resizeAsideContainers); //This should unload on turbolinks..

		for(var i = 0; i < asides.length; i++) {
			let aside = asides[i];
			aside.classList.add('invisible');
			aside.classList.add('enabled');
			aside.classList.add('hidden');
			let parent = aside.parentNode;
			parent.classList.add('hidden');
			setAsideContainerSize(parent, aside);
			let ref = '#' + aside.id;
			let links = document.querySelectorAll('a[href="'+ref+'"]');
			for(var j = 0; j < links.length; j++) {
				let link = links[j];
				link.classList.add('hasaside');
				link.id = aside.id + '-ref';
				link.addEventListener('click', function(e) {
					if(toggleActiveAside(aside, link)) {
						e.preventDefault(); //prevent default on hide of active
					}
				});
			}
			asideList.push({
				'aside' : asides[i],
				'links' : links,
				'active' : false,
			});
		}
	}

	let close_buttons = document.querySelectorAll('aside a[data-action="close"]');

	for(var i = 0; i < close_buttons.length; i++) {
		close_buttons[i].addEventListener('click', function(event) {
			event.preventDefault();
			let aside = document.getElementById(event.currentTarget.dataset.id);
			hideAside(getAsideFromList(aside));
			hideLinks();
		});
	}

}

function getAsideFromList(aside) {
	for (var i = asideList.length - 1; i >= 0; i--) {
		if(asideList[i].aside === aside) { return asideList[i]; }
	}
	return false;
}

function hideActiveAsides() {
	for(var i = 0; i < asideList.length; i++) {
		let an_aside = asideList[i];
		if(an_aside.active) {
			hideAside(an_aside);
		}
	}
}

function hideAside(aside) {
	aside.aside.classList.add('invisible');
	window.setTimeout(function() {
		aside.aside.classList.add('hidden');
		let parent = aside.aside.parentNode;
		parent.classList.add('hidden');
		let slot = aside.aside.closest('.slot');
		if(asideIsOpeningInSlot !== slot) {
			slot.style.height = 'auto'; //Dont set to auto, remove asideisopening stuff, just set to height correct for currently open
		}
	}, 200);
	aside.active = false;
}

function showAside(aside) {
	aside.aside.classList.remove('hidden');
	aside.aside.parentNode.classList.remove('hidden');
	let slot = aside.aside.closest('.slot');
	window.setTimeout(function() {
		aside.aside.classList.remove('invisible');
		resizeAsideContainers();
	}, 1);
	aside.active = true;
}

function hideLinks() {
	let allAsideLinks = document.querySelectorAll('.hasaside');
	for (var i = allAsideLinks.length - 1; i >= 0; i--) {
		allAsideLinks[i].classList.remove('active');
	}
}

function toggleActiveAside(aside, link) {
	for (var i = asideList.length - 1; i >= 0; i--) {
		if(asideList[i].aside === aside) {
			let current = asideList[i];
			if(current.active) {
				asideIsOpeningInSlot = false;
				hideAside(current);
				for (var i = current.links.length - 1; i >= 0; i--) {
					current.links[i].classList.remove('active');
				}
				return true;
			} else {
				hideActiveAsides();
				hideLinks();
				asideIsOpeningInSlot = current.aside.closest('.slot');
				showAside(current);
				current.aside.style.top = (link.offsetTop - marginTop) + 'px';
				for (var i = current.links.length - 1; i >= 0; i--) {
					current.links[i].classList.add('active');
				}
				return false;
			}
		}
	}
}

function setAsideContainerSize(el, container) {
	let slot = el.closest('.slot');
	// Only do this on desktop, where width is relevant
	style = window.getComputedStyle(slot),
    position = style.getPropertyValue('position');
	if(position != 'relative') { return; } 
	el.style.height = container.offsetHeight - marginBottom + 'px';
	el.style.width = slot.clientWidth + 'px';
}

function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}

function resizeAsideContainers() {
	for(var i = 0; i < asides.length; i++) {
		let aside = asides[i];
		let slot = aside.closest('.slot');
		
		//We only need to do this if position relative is set (on desktop)
		style = window.getComputedStyle(slot),
    	position = style.getPropertyValue('position');
		if(position == 'relative' && !aside.classList.contains('invisible')) {
			var maxHeight = Math.max(slot.offsetHeight, aside.offsetHeight);
			slots = slot.closest('.layout').querySelectorAll('.slot');
			if(slots.length > 1) {
				for (var j = slots.length - 1; j >= 0; j--) {
					let a_slot = slots[j];
					console.log('calculating height' + a_slot.offsetHeight + ' compares to old ' + maxHeight);
					maxHeight = Math.max(maxHeight, a_slot.offsetHeight);
				}
			}
			slot.style.height = maxHeight  + 'px';
		}
		let parent = aside.parentNode;
		setAsideContainerSize(parent, slot);
	}
}
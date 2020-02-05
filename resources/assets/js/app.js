/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

if (!Element.prototype.matches) Element.prototype.matches = Element.prototype.msMatchesSelector;
if (!Element.prototype.closest) Element.prototype.closest = function(selector) {
    var el = this;
    while (el) {
        if (el.matches(selector)) {
            return el;
        }
        el = el.parentElement;
    }
};

var Turbolinks = require("turbolinks");
Turbolinks.start();

var onLoadHandlers = [];
var onUnloadHandlers = [];

document.addEventListener('turbolinks:click', function(e) {
    if ((e.srcElement || e.target).classList.contains('hasaside')) {
        e.preventDefault();
    }
});

document.addEventListener('turbolinks:request-start', function(e) {
    for (var i = onLoadHandlers.length - 1; i >= 0; i--) {
        callback(onUnloadHandlers[i]);
    }
    main = document.getElementById('content');
    main.classList.add('opening');
});

document.addEventListener('turbolinks:before-cache', function(e) {
    main = document.getElementById('content');
    main.classList.add('opening');
});

document.addEventListener('turbolinks:load', function(e) {
    for (var i = onLoadHandlers.length - 1; i >= 0; i--) {
        callback(onLoadHandlers[i]);
    }
    setupMenuListener();
    setupDetailsListener();
    setupAdminListener();
    setupDiscussions();
    setupSortable();

    content = document.getElementById('content');
    content.classList.add('opening');
    window.setTimeout(function() {
        content.classList.remove('opening');
    }, 100);

    document.querySelector('img.lightbox').addEventListener('click', function(e) {
        const instance = basicLightbox.create('<img src="' + e.currentTarget.src + '" />').show()
    });
});

function setupMenuListener() {
    let menuLink = document.getElementById('menu');
    let body = document.getElementsByTagName('body');
    body[0].classList.toggle('menu-hidden');
    menuLink.addEventListener('click', function(e) {
        e.preventDefault();
        body[0].classList.toggle('menu-hidden');
    });
}

function setupDetailsListener() {
    let details = document.querySelectorAll('[data-type=details-block]');
    for (var i = details.length - 1; i >= 0; i--) {
        let detail = details[i];
        let detailBlocks = detail.querySelectorAll('[data-type=details]');
        if (detailBlocks.length == 0) { continue; }
        let detailBlock = detailBlocks[0];
        let originalHeight = detailBlock.offsetHeight;
        detail.classList.remove('expanded');
        detailBlock.style.height = "0px";
        let toggles = details[i].querySelectorAll('a[data-type=details-toggle]');
        for (var j = toggles.length - 1; j >= 0; j--) {
            toggles[j].addEventListener('click', function(e) {
                e.preventDefault();
                if (detail.classList.contains('expanded')) {
                    detailBlock.style.height = "0px";
                    detail.classList.remove('expanded');
                } else {
                    detailBlock.style.height = originalHeight + 'px';
                    detail.classList.add('expanded');
                }
            });
        }
    }
}

function setupAdminListener() {
    if (document.querySelectorAll('body.isediting').lenght == 0) { return; }
    setupAdminPopupListener();
    let page_checkbox = document.querySelectorAll('input[name=is_page]');
    let page_options = document.getElementById('page-options');
    if (page_checkbox.length > 0) {
        page_checkbox[0].addEventListener('change', function(e) {
            if (e.target.checked) {
                page_options.classList.remove('admin-hidden');
            } else {
                page_options.classList.add('admin-hidden');
            }
        });
    }
}

function setupAdminPopupListener() {
    let popups = document.querySelectorAll('[data-popup]');
    for (var i = popups.length - 1; i >= 0; i--) {
        let popup = popups[i];
        let menu = document.getElementById(popup.getAttribute('data-popup'));
        menu.classList.add('admin-hidden');
        popup.addEventListener('click', function(e) {
            e.preventDefault();
            menu.classList.toggle('admin-hidden');
        });
    }
}

function setupDiscussions() {
    let discussions = document.querySelectorAll('.node.discussion');
    for (var i = discussions.length - 1; i >= 0; i--) {
        let discussion = discussions[i];
        let forms = discussion.querySelectorAll('form');
        let lists = discussion.querySelectorAll('.messagelist');
        let scroll = lists[0].querySelectorAll('.scroll');
        let w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        let h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
        let listDimensions = lists[0].getBoundingClientRect();
        let formDimensions = forms[0].getBoundingClientRect();
        scroll[0].style.maxHeight = h - listDimensions.top - formDimensions.height + 'px';
        scroll[0].scrollTop = scroll[0].scrollHeight;
    }
}

function setupSortable() {
    var Sortable = require("sortablejs");
    var els = document.querySelectorAll('.sortable');
    for (var i = els.length - 1; i >= 0; i--) {
        let node = els[i].getAttribute('data-node');
        let field = els[i].getAttribute('data-field');
        let groupName = (els[i].getAttribute('data-group')) ? els[i].getAttribute('data-group') : '';
        let input = document.getElementById(field);
        var sortable = Sortable.create(els[i], {
            url: els[i].getAttribute('data-url'),
            group: groupName,
            filter: '.nosort',
            store: {
                get: function(sortable) {
                    var order = input.value;
                    order = (order) ? order.split(',') : [];
                    input.value = (order.length > 0) ? order.join(',') : sortable.toArray().join(',');
                    return order;
                },
                set: function(sortable) {
                    var order = sortable.toArray();
                    input.value = order.join(',');
                }
            },
            onSort: function(event) {
                var order = this.toArray();
                input.value = order.join(',');
                if (this.options.url !== null) {
                    console.log(this.options.url);
                    postData(this.options.url, {'order' : order});
                }
            }
        });
    }
}

function postData(url, data) {
    var request = new XMLHttpRequest();
    request.open('POST', url, true);

    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/json');
    request.onload = function() {
      if (this.status >= 200 && this.status < 400) {
        // Success!
        var data = JSON.parse(this.response);
      } else {
        console.log('error');
      }
    };
    request.onerror = function() {
      // There was a connection error of some sort
    };
    request.send(JSON.stringify(data));
}


var tinymce = require('tinymce/tinymce');

// A theme is also required
require('tinymce/themes/modern/theme');

// Any plugins you want to use has to be imported
require('tinymce/plugins/code');
require('tinymce/plugins/autoresize');
require('tinymce/plugins/link');
require('tinymce/plugins/image');
require('tinymce/plugins/lists');
require('tinymce/plugins/media');
require('tinymce/plugins/table');
const basicLightbox = require('basicLightbox')
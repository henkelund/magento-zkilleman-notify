var ZkillemanNotify = Class.create();

ZkillemanNotify.prototype = {
    _interval: null,
    _messages: null,
    _popped: null,
    _counter: null,
    _config: null,
    initialize: function(config) {
        this._messages = new Array();
        this._popped = new Array();
        this._counter = 0;
        this._config = config;
    },
    addMessage: function(message) {
        if (message && message.text) {
            if (!message.type) {
                message.type = 'notice';
            }
            message.age = 0;
            message.id = this._counter++;
            message.elemId = 'zn-' + message.id;
            this._messages.push(message);
        }
        this._startEventLoop();
    },
    popMessage: function() {
        if (!this._messages || !this._messages.length) {
            return;
        }

        var message = this._messages.shift();
        this._popped.push(message);

        message.elem = new Element(
            'li', {id: message.elemId}
        ).insert(message.text);
        message.elem.addClassName(message.type);
        Event.observe(message.elem, 'mouseover', function(event) {
            message.elem.addClassName('hover');
        });
        Event.observe(message.elem, 'mouseout', function(event) {
            message.elem.removeClassName('hover');
        });
        Event.observe(message.elem, 'mousemove', function(event) {
            message.age = 0;
            if (message.effect) {
                message.effect.cancel();
                message.effect = false;
                message.elem.setStyle({opacity: 0.95});
            }
        });

        var closeButton = new Element('img', {src: this._config.closeButton});
        message.elem.insert(closeButton);
        Event.observe(closeButton, 'click', function(event) {
            new Effect.Opacity(message.elem, {
                duration: 0.5,
                from: 0.95,
                to: 0,
                afterFinish: function() {
                    message.elem.hide();
                }
            });
        });

        message.elem.setStyle({opacity: 0});
        this._config.listElem.insert(message.elem);
        new Effect.Opacity(message.elem, {
            duration: 0.25,
            from: 0,
            to: 0.95
        });
    },
    _iteration: function() {
        if (this._popped && this._popped.length) {
            for (var i = 0; i < this._popped.length; ++i) {
                if (++this._popped[i].age > 10 && !this._popped[i].effect) {
                    this._popped[i].effect = new Effect.Opacity(this._popped[i].elem, {
                        duration: 5,
                        from: 0.95,
                        to: 0,
                        afterFinish: function(e) {
                            var elem = $(e.element);
                            for (var j = 0; j < this._popped.length; ++j) {
                                if (this._popped[j].elemId == elem.identify()) {
                                    this._popped.splice(j, 1);
                                    elem.hide();
                                }
                            }
                        }.bind(this)
                    });
                }
            }
        }

        this.popMessage();
    },
    _startEventLoop: function() {
        if (this._interval == null) {
            this._interval = window.setInterval(this._iteration.bind(this), 500);
        }
    }
};

UI.DragManager = Base.extend({
    
    _source: false,
    _dest: false,
    
    _sourceElements: false,
    _destElements: false,
    _container: false,
    
    constructor: function(container, source, dest) {
        this.base();
        
        this._container = container;
        this._sourceElements = [];
        this._destElements = [];
        
        this.dragSource(source);
        this.dragDestination(dest);
    },
    
    dragSource: function(source) {
        if(source == undefined)
            return this._source;
        else {
            this._source = source;
            source.dragManager(this);
            var self = this;
            this._source.addHandler('elementsAdded', function(sender, args) {
                self.__addSourceElements(sender, args);
            });
            return this;
        }
    },
    
    dragDestination: function(dest) {
        if(dest == undefined)
            return this._dest;
        else {
            this._dest = dest;
            dest.dragManager(this);
            var self = this;
            this._dest.addHandler('elementsAdded', function(sender, args) {
                self.__addDestElements(sender, args);
            });
            return this;
        }
    }, 
    
    __addDestElements: function(sender, args) {
        this._destElements = this._destElements.concat(args.elements);
        this.Render();
    },
    
    __addSourceElements: function(sender, args) {
        this._sourceElements = this._sourceElements.concat(args.elements);
        this.Render();
    },
    
    Render: function() {
        var self = this;
        
        $(this._sourceElements).draggable({
            cursor: "copy", 
            cursorAt: { top: -12, left: -20 },
            helper: function(event) {
                return $('<div class="ui-draggable-helper">' + self._source.getDragElement($(event.currentTarget)) + '</div>');
            },
            appendTo: this._container,
            opacity: 0.7, 
            connectToSortable: $(this._destElements),
            start: function(event, ui) {
                $(':focus').blur();
                self.raiseEvent('dragStart', {domEvent: event, ui: ui});
            }, 
            stop: function(event, ui) {
                self.raiseEvent('dragEnd', {domEvent: event, ui: ui});
            },
            drag: function(event, ui) {
                self.raiseEvent('dragProcess', {domEvent: event, ui: ui});
            }
        });    
    },
    

});
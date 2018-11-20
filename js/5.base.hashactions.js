
HashActions = Base.extend({
    
    options: null,
    
    handlers: null,
    
    constructor: function(options) {
        this.base();
        this.options = options !== undefined ? options : {};
        this.init();
        this.handlers = {};
    },
    
    init: function() {
        var self = this;
        window.addEventListener('hashchange', function() {
            self.handleAction(location.hash.substr(1));
        });
    }, 
    
    handleDomReady: function() {
        this.initDOMHandlers();
        this.handleAction(location.hash.substr(1));
    }, 
    
    initDOMHandlers: function() {
        if(!$) return;
        $(document).on('click', 'a[data-action]', function() {
            location.hash = '#' + $(this).data('action');
            return false;
        }); 
        
    },
    
    addActionHandler: function(action, handler) {
        if(this.handlers[action] === undefined)
            this.handlers[action] = [];
        this.handlers[action].push(handler);
    },
    
    raiseAction: function(action, args) {
        try {
            if(this.handlers[action] === undefined)
                return false;
            
            var self = this;
            var handlers = this.handlers[action];
            handlers.forEach(function(handler, index, array) {
                handler.apply(self, [action, args]);
            });
        }
        catch(e) { console.log('no action handler ' + action + ', exception: ' + e); }
        
    }, 
    
    handleAction: function(actionString) {
        
        var queryString = actionString.toObject('&=');
        if(queryString.action == undefined)
            return  false;
        
        history.replaceState ? 
            history.replaceState("", document.title, window.location.pathname + window.location.search) 
                :
            history.pushState("", document.title, window.location.pathname + window.location.search);
        
        this.raiseAction(queryString.action, queryString);
        
    },
    
});

window.hashActions = new HashActions();
$().ready(function() {
    window.hashActions.initDOMHandlers();
});
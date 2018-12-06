UI.Controls.Label = UI.Controls.Control.extend({
    
    constructor: function(name, container) {
        this.base(name, container);
    }, 
    
    Render: function() {
        var self = this;
        this.base('span', 'ui-label');
        
        this._element.click(function() {
            if(!$(this).is(':disabled'))
                self.raiseEvent('click', []);
        });
        
        this.raiseEvent('ready', []);
        return this;
    },
    
    val: function(val) {
        if(val == undefined)
            return this._element.html();
        else
            this._element.html(val);
    },
    
}, {});
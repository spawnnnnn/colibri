Template = Base.extend({
    
    name: '',
    
    constructor: function(name) { this.base(); this.name = name; },
    
    Render: function(object) { 
        var className = this.name + 'Template';
        if(!eval(className))
            return ;
        
        var t = eval('new ' + className + '(object)');
        t.Run();
        return t.Return(); 
    },
    
}, {
    
    Create: function(name) {
        return new Template(name);
    }
    
});

TemplateRenderer = Base.extend({
    
    object: null,
    context: null,
    
    constructor: function(object) {
        this.base();
        this.context = [];
        this.object = object;
    },
    
    Return: function(start, split, end) {
        start = start || '', split = split || '', end = end || '';
        return start + this.context.join(split) + end;
    }
    
});
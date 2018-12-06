UI.Controls.Charts = UI.Controls.Control.extend({
    
    
    
});

UI.Controls.FunnelChart = UI.Controls.Charts.extend({
    
    _data: false,
    _theme: false,
    
    constructor: function(name, container) {
        this.base(name, container);
        this._theme = {
            barColors: ['#0046e4', '#e4003b', '#068700', '#e9aa00', '#9900e9', '#920000'],
            barTitleStyle: {color: '#000', fontSize: '12px', fontWeight: 'normal', textAlign: 'right'},
            titleStyle: {color: '#000', fontSize: '18px', fontWeight: 'bold', textAlign: 'center', display: 'flex', flexDirection: 'column', justifyContent: 'center'}
        };
    },
    
    Render: function() {
        
        this.base('div', 'ui-chart ui-funnel-chart');
        this.width('100%').height('100%');
        
        this._element.append('<div class="ui-charts-title"></div>');
        this._element.append('<div class="ui-charts-content"></div>');
        
        this._element.find('.ui-charts-title').css(this._theme.titleStyle).hide();
        
        this.bind();
        
        return this;
        
    },
    
    bind: function() {
        var self = this;
        $(window).on('resizeend', function() {
            self._element.find('.ui-funnel-bar').hide();
            setTimeout(function() {
                self.Resize();
                self._element.find('.ui-funnel-bar').show();
            }, 10);
        });

    },
    
    Resize: function() {
        
        if(!this._data)
            return;
        
        var self = this;
        
        var index = 0;
        var count = self._data.length;
        var maxWidth = 70;
        var height = self._element.find('.ui-charts-content').height();
        var maxValue = 0;
        self._data.forEach(function(bar) {
            if(parseInt(bar.value) > maxValue)
                maxValue = parseInt(bar.value);
        });
        self._data.forEach(function(bar) {
            
            var style = {
                height: height/self._data.length + 'px',
            };
            
            var curValue = parseInt(parseInt(bar.value)*100/maxValue);
            if(isNaN(curValue)) curValue = 0;
            var style2 = {
                width: curValue + '%',
                opacity: 0.5,
            }
            
            self._element.find('.ui-funnel-bar:eq(' + index + ')').css(style)
            self._element.find('.ui-funnel-bar:eq(' + index + ')>div').css(style2)
            self._element.find('.ui-funnel-bar:eq(' + index + ')>span').css(style)
            
            index++;
        });
        return this;    
    },
    
    generate: function(data) {
        var self = this;
        this._data = data;
        
        var color = 0;
        this._data.forEach(function(bar) {
            var barControl = $('<div class="ui-funnel-bar"><span class="ui-finnel-bar-title">' + bar.title + '</span><div></div></div>');   
            barControl.find('>span').css(self._theme.barTitleStyle);
            barControl.find('>div').css({backgroundColor: self._theme.barColors[color == self._theme.barColors.length ? (color=0) : color++]});
            self._element.find('.ui-charts-content').append(barControl);                                                       
        });                       
        
        return this;
    },
    
    title: function(value) {
        if(value == undefined)
            return this._element.find('.ui-charts-title').html();
        else {
            this._element.find('.ui-charts-title').html(value);
            if(value)
                this._element.find('.ui-charts-title').show();
            else
                this._element.find('.ui-charts-title').hide();
            return this;
        }
    },
    
    
});
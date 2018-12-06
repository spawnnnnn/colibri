UI.Controls.Window.Threading = UI.Controls.Window.extend({
    
    _process: false,
    _params: false,
    _logTimeout: 2000,

    constructor: function(container, title, draggable, worker, timeout, prio, params, logTimeout) {
        this.base(container, title, draggable);
        this._process = new Threading.Process(worker, timeout, prio);
        this._params = params;
        this._logTimeout = logTimeout ? logTimeout : 2000;
        this.bind();
    },

    bind: function() {
        var self = this;
        this._process
            .addHandler('process.error', function(sender, args) {
                self.addResult('process error:', args.message); 
            })
            .addHandler('process.started', function(sender, args) {
                self._addResult('process is runned successfuly'); 
                this.StartProcessLog(self._logTimeout);
            })
            .addHandler('process.completed', function(sender, args) {
                self._addResult('process complete'); 
                self.controls('start').enable(true);
                self.controls('cancel').enable(true);
            })
            .addHandler('process.stoped', function(sender, args) {
                self._addResult('process stoped'); 
            })
            .addHandler('process.log', function(sender, args) {
                args.results.forEach(function(result) {
                    self._addResult(result); 
                })
            });

        this
            .addHandler('ready', function(sender, args) {
                var self = this;
                self.Resize(750, 0);

                if(this._params && this._params.length) {
                    this._params.forEach(function(param) {
                        self.addControl(param);
                    });
                }

                this.controls('resultLabel', new UI.Controls.Label('resultLabel', this.container('content'))).parent(this).Render().html('Результаты').styles({fontWeight: 'bold', backgroundColor: 'transparent', border: '0px', paddingBottom: '5px'});
                this.controls('result', new UI.Controls.Pane('result', this.container('content'))).parent(this).Render().styles({width: '100%', height: '400px'});
                this.controls('start', new UI.Controls.Button('start', this.container('buttons'), 'Запустить', false, 'start').addHandler('click', function() { self.raiseEvent('dialogResult', {result: 'start'}); })).parent(this).Render();
                this.controls('cancel', new UI.Controls.Button('cancel', this.container('buttons'), 'Отменить', false, 'cancel').addHandler('click', function() { self.raiseEvent('dialogResult', {result: 'cancel'}); })).parent(this).Render();
                
            })
            .addHandler('dialogResult', function(sender, args) {
                switch(args.result) {
                    case 'start': {
                        this.controls('start').enable(false);
                        this.controls('cancel').enable(false);

                        var params = {};
                        var self = this;
                        this._params.forEach(function(param) {
                            params[param.field] = self.controls(param.field).val();
                        });

                        this._process.StartProcess(params);
                        break;
                    }
                    case 'cancel': {
                        this.Hide();
                        break;
                    }
                }
            });

    },

    _addResult: function(result) {
        var label = new UI.Controls.Label(Date.Now().getTime(), this.controls('result')).Render().styles({display: 'block', width: '100%', fontSize: '14px', padding: '5px'}).html(result);
        label.ensureVisible();
    },

});
/**
 * Класс обертка для запроса на создание процесса на сервере
 * 
 * пример использования: 
 * 
 * var thread = new Threading.Process('TestWorker', 1000, 0);
 * thread.
 *      .addHandler('process.error', function(sender, args) {
 *          out('error: ->', args.message); 
 *      })
 *      .addHandler('process.started', function(sender, args) {
 *          out('->', 'process is runned successfuly');
 *          this.StartProcessLog();
 *      })
 *      .addHandler('process.completed', function(sender, args) {
 *          out('->', 'process complete');
 *      })
 *      .addHandler('process.stoped', function(sender, args) {
 *          out('->', 'process stoped');
 *      })
 *      .addHandler('process.log', function(sender, args) {
 *          args.results.forEach(function(result) {
 *              out('log: ---->', result);      
 *          })
 *      })
 *      StartProcess();
 * 
 */
Threading.Process = JsApi.extend({

    /**
     * Лимит по времени для работы процесса
     */
    _timeLimit: 0,

    /**
     * Приоритет процесса
     */
    _prio: 0,

    /**
     * Наименование воркера
     */
    _name: '',

    /**
     * Информация о запущенном воркере
     */
    _runningWorker: false,

    /**
     * PID процесса
     */
    _pid: false,

    /**
     * Количество прочитанных строк
     */
    _currentReadPosition: 0,

    /**
     * Таймер запроса лога
     */
    _logTimerInterval: -1,

    /**
     * Конструктор
     * @param {string} name 
     * @param {integer} timeLimit 
     * @param {integer} prio 
     */
    constructor: function(name, timeLimit, prio) {
        this.base();
        this._timeLimit = timeLimit;
        this._name = name;
        this._prio = prio;
    }, 

    /**
     * Запуск процесса
     * @param {object} params 
     */
    StartProcess: function(params) {
        var self = this;
        if(!params) params = {};
        this._request('ProcessAjaxHandler.StartProcess', {worker: JSON.stringify({name: this._name, timelimit: this._timeLimit, prio: this._prio}), params: JSON.stringify(params)}, function(data) {
            if(data.error) {
                self.raiseEvent('process.error', {message: data.message});
                return;
            }
            self._pid = data.process;
            self._runningWorker = data.worker;
            self.raiseEvent('process.started', {process: data.process, worker: data.worker});
        });
        return this;
    },

    /**
     * Останавливает процесс на сервере
     */
    StopProcess: function() {
        var self = this;
        this._request('ProcessAjaxHandler.StopProcess', {process: this._pid}, function(data) {
            if(data.error) {
                self.raiseEvent('process.error', {message: data.message});
                return;
            }
            self.StopProcessLog();
            self.raiseEvent('process.stoped', {result: data.result});
        });
    },

    /**
     * Проверяет запущен ли процесс на сервере
     * @param {callback} callback 
     */
    IsRunning: function(callback) {
        var self = this;
        this._request('ProcessAjaxHandler.ProcessIsRunning', {process: this._pid}, function(data) {
            if(data.error) {
                self.raiseEvent('process.error', {message: data.message});
                return;
            }
            if(callback) {
                callback.apply(self, [data.result]);
            }
        });
    },

    /**
     * Запускает процесс загрузки лог файла
     * @param {integer} timer 
     */
    StartProcessLog: function(timer) {
        var self = this;
        this._logTimerInterval = setInterval(function() {
            self._request('ProcessAjaxHandler.ProcessLog', {process: self._pid, log: self._runningWorker.log, position: self._currentReadPosition}, function(data) {
                if(data.error) {
                    self.raiseEvent('process.error', {message: data.message});
                    return;
                }
                self._currentReadPosition = data.position;
                self.raiseEvent('process.log', {results: data.results});
                if(!data.isrunning) {
                    self.StopProcessLog();
                    self.raiseEvent('process.completed');
                }
            });
        }, timer ? timer : 5000);
    },

    /**
     * Останавливает таймер лога
     */
    StopProcessLog: function() {
        clearTimeout(this._logTimerInterval);
        this._logTimerInterval = -1;
        this._currentReadPosition = 0;
    },

});
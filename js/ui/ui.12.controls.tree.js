UI.Controls.Tree = UI.Controls.Control.extend({
    
    _nodesList: false,
    _nodes: false,
    
    _selected: false,
    _draggable: false,
    _dragManager: false,
    
    _cm: false,
    _contextmenu: false,
    
    constructor: function(name, container, draggable) {
        this.base(name, container);
        this.addHandler('nodeClicked', this.__nodeClicked);
        this._nodesList = [];
        this._draggable = draggable != undefined ? draggable : false;
    },
    
    Render: function() {
        this.base('div', 'ui-tree');
        this.tabIndex(true);
        this._nodes = (new UI.Controls.TreeNodes('nodes', this, this)).Render();
        
        
        this.bindHtmlEvents();
        this.bind();
        
        
        return this;
    },

    bind: function() {
        var self = this;

        this.addHandler('contextMenuClicked', function(sender, args) {
            var node = this;
            
            var position = this._element.find('>.contextmenu').offset();
            position.left += this._element.find('>.contextmenu').outerWidth();   
            
            this._cm = new UI.Controls.ContextMenu('nodecm', $(document.body)).orientation('left top');
            this._cm
                .addHandler('menuItemClicked', function(sender, args) {
                    node._cm.Hide().Dispose();
                    node.raiseEvent('contextMenuItemClicked', {node: node, menuItem: args.item, menuItemData: args.itemData});
                })
                .addHandler('shown', function() {
                    self.raiseEvent('contextMenuShown', {node: false});
                })
                .Render()
                .styles({minWidth: '250px'})
                .addItems(node.contextmenu())   
                .Show(position);
        });
        
    }, 

    bindHtmlEvents: function() {
        
        var self = this;

        this._element.click(function(e) {
            
            if($(e.target).is('.ui-tree')) {
                self.clearSelection();
                self.raiseEvent('clicked');
                self.raiseEvent('selectionChanged', {node: false});
            }
            return false;
            
        });

        this._element.keydown(function(e) {
            
            var selected = self.selected();
            if(!selected) {
                self.selected(self.nodes().controls('firstChild'));
                return false;
            }
            
            var prev = selected ? selected.prev() : false;
            var next = selected ? selected.next() : false;
            var parentNode = selected ? selected.parentNode() : false;
            if(parentNode && !parentNode.tree) parentNode = false;

            switch(e.keyCode) {
                case 38: { // вверx

                    if(prev) {
                        while(prev.expanded()) { prev = prev.nodes().controls('lastChild'); }
                        self.selected(prev);
                    }
                    else if(parentNode) {
                        self.selected(parentNode);
                    }
                    
                    return false;

                }
                case 40: { // вниз

                    function parentNext(node) {
                        while(node.next && !node.next()) { node = node.parentNode(); }
                        return !node.next ? false : node.next();
                    }
                    
                    if(selected.expanded() && selected.nodes().count() > 0) {
                        self.selected(selected.nodes().controls('firstChild'));
                    }
                    else if(next) {
                        self.selected(next);
                    }
                    else if(parentNode && (parentNode = parentNext(selected))) {
                        self.selected(parentNode);
                    }
                    
                    return false;
                
                }
                case 39: { // right 
                    
                    if(!selected.expanded())  {
                        selected.Expand();
                    }
                    return false;

                }
                case 37: { // left 
                    if(selected.expanded())  {
                        selected.Collapse();
                    }
                    else if(parentNode && parentNode.next) {
                        self.selected(parentNode);
                    }
                    return false;
                }
                case 32: { // space
                    if(selected.contextmenu()) {
                        selected.raiseEvent('contextMenuClicked');
                    }
                    return false;
                }
                case 36: { // home
                
                }
                case 35: { // end
                
                }
                case 13: { // enter
                    selected._element.find('.text').click();
                    return false;
                }
            }

            return true;            
        });
    },
      
    nodes: function() {
        return this._nodes;
    },
    
    clearSelection: function() {
        $(this._nodesList).each(function(i, o) {
            o.removeClass('selected');
        });    
        this._selected = false;    
    }, 
    
    __nodeClicked: function(sender, args) {
        this.selected(args.node);
    },      
    
    selected: function(node) {
        
        if(node === undefined) {
            return this._selected;
        }
        else if(node === false) {
            var changed = this.selected() !== false;
            this.clearSelection();
            if(changed) 
                this.raiseEvent('selectionChanged', {node: false});
        
        }
        else { 

            var changed = false;
            if(!this.selected() || (this.selected().path() != node.path())) {
                changed = true;
            }               

            this.clearSelection();
            node.addClass('selected');
            if(node.Expand)
                node.Expand();

            this._selected = node;
            if(changed) 
                this.raiseEvent('selectionChanged', {node: node});

            return this;
        }
        
    },
    
    nodesList: function() {
        return this._nodesList;
    },
    
    draggable: function() {
        return this._draggable;
    },
    
    path: function() {
        return '/';
    },
    
    prepareForNodeDrag: function() {
        this._element.find('.ui-treenodes').addClass('moving');
    },
    
    endNodeDrag: function() {
        this._element.find('.ui-treenodes').removeClass('moving');
    },
    
    clearHighlights: function() {
        this._element.find('.ui-treenode.highlighted').removeClass('highlighted');
    },
    
    dragManager: function(value) {
        if(value == undefined)
            return this._dragManager;
        else {
            this._dragManager = value;
            return this;
        }
    },
    
    getDragElement: function(el) {
        return el.find('>p').html();
    },

    contextmenu: function(menu) {
        if(menu === undefined)
            return this._contextmenu;
        else {
            this._contextmenu = menu;
            this._createMenuPointer();
            return this;
        }
    },

    _createMenuPointer: function() {
        
        if(this._contextmenu) {
            var self = this;                   
            if(this._element.find('>p .contextmenu').length == 0) {
                this._element.append('<span class="contextmenu">•••</span>');
                this._element.find('>.contextmenu').click(function(e) {
                    self.raiseEvent('contextMenuClicked', {});   
                    return false;
                });
                this._element.scroll(function() {
                self._element.find('>.contextmenu').css({bottom: -1 * self._element.scrollTop() + 5});
            });
            }
        }
        else {
            this._element.find('>p .contextmenu').remove();
        }
        
    },
    
    ExpandAll: function() {
        this._nodesList.forEach(function(v) {
            v.Expand();
        });
    }, 
    
    CollapseAll: function() {
        this._nodesList.forEach(function(v) {
            v.Collapse();
        });
    },
    
});

UI.Controls.TreeNode = UI.Controls.Control.extend({
    
    _tree: false,
    _nodes: false,
    _contextmenu: false,
    
    _editable: false,
    
    _cm: false,
    
    constructor: function(name, container, tree) {
        this.base(name, container);
        this._tree = tree;           
    },
    
    Render: function() {
        if(this._element)
            return this;
        
        this.base('li', 'ui-treenode');
        
        this._element.append('<p><em class="pointer"></em><em class="icon"></em><span class="text"></span></p>');
        this._nodes = (new UI.Controls.TreeNodes('nodes', this, this._tree)).Render();
        
        var self = this;
        this._element.find('.pointer').click(function() {
            self._tree.focus();
            if(self.expanded())
                self.Collapse();
            else
                self.Expand();
            self._tree.raiseEvent('pointerClicked', {node: self});
            return false;
        });
        
        this._element.find('.text,.icon').click(function() {
            self._tree.focus();
            self._tree.raiseEvent('nodeClicked', {node: self});
            return false;
        }).dblclick(function() {
            self.Edit();
        });
        
        this.addHandler('contextMenuClicked', function(sender, args) {
            self._tree.focus();
            var node = this;
            
            var position = this._element.find('.contextmenu').offset();
            position.top += this._element.find('.contextmenu').height();   
            
            this._cm = new UI.Controls.ContextMenu('nodecm', $(document.body));
            this._cm
                .addHandler('menuItemClicked', function(sender, args) {
                    node._cm.Hide().Dispose();
                    node.tree().focus();
                    node.tree().raiseEvent('contextMenuItemClicked', {node: node, menuItem: args.item, menuItemData: args.itemData});
                })
                .addHandler('shown', function() {
                    self.tree().raiseEvent('contextMenuShown', {node: node});
                })
                .addHandler('hidden', function() {
                    self.tree().focus();
                })
                .Render()
                .styles({minWidth: '250px'})
                .addItems(node.contextmenu())
                .Show(position);
        });
                             
        
        return this;
    },
    
    _createMenuPointer: function() {
        
        if(this._contextmenu) {
            var self = this;
            if(this._element.find('>p .contextmenu').length == 0) {
                this._element.find('>p').append('<span class="contextmenu">•••</span>');
                this._element.find('>p .contextmenu').click(function(e) {
                    self.raiseEvent('contextMenuClicked', {node: self});   
                    return false;
                });
            }
        }
        else {
            this._element.find('>p .contextmenu').remove();
        }
        
    },
    
    nodes: function() {
        return this._nodes;
    },    
    
    title: function(value) {
        if(value == undefined) {
            return this._element.find('>p>span.text').html();
        }
        else {
            this._element.find('>p>span.text').html(value);
            return this;
        }
    },    
    
    icon: function(value) {
        if(value == undefined) {
            return this._element.find('>p>em.icon').css('background-image').replaceAll('url(', '').replaceAll(')');
        }
        else {
            this._element.find('>p>em.icon').css('background-image', 'url(' + value + ')');
            return this;
        }
    }, 
    
    expanded: function() {
        return this._element.hasClass('expanded');
    },
    
    Expand: function() {
        if(this._element && this.nodes().count() > 0) {
            this._element.addClass('expanded');
            this._tree.raiseEvent('nodeExpanded', {node: this});
        }
    }, 
    
    Select: function() {
        this._element.find('.text').click();
        var node = this;
        while(node = node.parentNode()) {
            if(node instanceof UI.Controls.Tree)
                return this;
            node.Expand();
        }
        return this;
    },
                                           
    Collapse: function() {
        if(this._element) {
            this._element.removeClass('expanded');
            this._tree.raiseEvent('nodeCollapsed', {node: this});
        }
    }, 
    
    path: function() {
        
        var ret = [];
        var s = this;
        while(!s._nodesList) {
            if(s.name() != 'nodes')
                ret.push(s.name());
            s = s.parent();
        }
        ret = ret.reverse();
        return ret.join('/');
    }, 
    
    level: function() {
        
        var ret = 0;
        var s = this;
        while(!s._nodesList) {
            if(s.name() != 'nodes') 
                ret++; 
            s = s.parent();
        }
        return ret;
    },   
    
    contextmenu: function(menu) {
        if(menu === undefined)
            return this._contextmenu;
        else {
            this._contextmenu = menu;
            this._createMenuPointer();
            return this;
        }
    },
    
    tree: function() {
        return this._tree;
    },
    
    parentNode: function() {
        return this.parent().parent();
    },
    
    parentNodes: function() {
        var ret = [];
        var s = this;
        while(!s._nodesList) {
            if(s.name() != 'nodes')
                ret.push(s);
            s = s.parent();
        }
        ret = ret.reverse();
        return ret;
    },
    
    iconStyles: function(value) {
        this._element.find('.icon').css(value);
        return this;
    },
    
    highlight: function(value) {
        if(value == true) {
            this._element.addClass('highlighted');
        }
        else {
            this._element.removeClass('highlighted');
        }
    },
    
    name: function(value) {
        
        if(value == undefined)
            return this._name;
        else {
            this.parent().controls(this._name, null);
            this._name = value;
            this.parent().controls(this._name, this);
            return this;
        }
        
    }, 
    
    Dispose: function() {
        
        var self = this;
        var nodeIndex = 0;
        $(this.tree().nodesList()).each(function(i, node) { if(node.path() == self.path()) { nodeIndex = i; return false; } return true; });
        this.tree()._nodesList.splice(nodeIndex, 1);

        // this.nodes().Dispose();
        delete this.parent().controls()[this._name];
        if(this.parent().count() == 0 && this.parentNode().tree) {
            this.parentNode().Collapse();
            this.parentNode().removeClass('expandable');
            
        }
        
        this.base();
        
    },
    
    editable: function(value) {
        if(value === undefined)
            return this._editable;
        else {
            this._editable = value;
            return this;
        }
    },
    
    Edit: function() {
        var self = this;
        if(this._editable) {
            
            var text = this._element.find('>p>.text');
            var val = text.html();

            text.hide();
            var input = $('<input type="text" style="width: 100%;" value="" />');
            text.after(input);   
            input.attr('cancel-with', val); 
            input.val(val).focus().blur(function() {
                var changed = false;
                if($(this).val()) {
                    text.html($(this).val());
                    changed = true;
                }                 
                else {
                    text.html($(this).data('cancelWith'));
                }
                $(this).remove();
                text.show();
                self.tree().raiseEvent('editableChanged', {node: self, oldValue: input.attr('cancel-with'), newValue: text.html()});
            }).keydown(function(e) {
                if(e.keyCode == 13 || e.keyCode == 27) {
                    if(e.keyCode == 27)
                        $(this).val('');
                    $(this).blur();
                    return false;
                }   
            }).ensureVisible();
        }
        else if(this._contextmenu) {
            var def = false;
            this._contextmenu.forEach(function(item) {
                console.log(item);
                if(item.def) {
                    def = item;
                    return false;
                }
            });
            if(def) {
                this.tree().raiseEvent('contextMenuItemClicked', {node: this, menuItem: def.key, menuItemData: def});
            }
        }
        else {
            this.tree().raiseEvent('nodeDoubleClicked', {node: this});
        }
    },

    next: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i + 1]];
            }    
        }
        
        return false;
    }, 
    
    prev: function() {
        
        var self = this;
        var controls = this.parent().controls();
        var keys = Object.keys(controls);
        for(var i=0; i<keys.length; i++) {
            if(controls[keys[i]] == self) {
                return controls[keys[i - 1]];
            }    
        }
        
        return false;
    },
    
});

UI.Controls.TreeNodes = UI.Controls.Control.extend({
    
    _tree: false,
    _sortiable: false,
    
    constructor: function(name, container, tree) {
        this.base(name, container);
        this._tree = tree;
    }, 
    
    Render: function() {
        this.base('menu', 'ui-treenodes');
        var self = this;
        if(this.tree().draggable()) {
            this.container().sortable({
                connectWith: '.ui-treenodes',
                over: function(event, ui) {
                    $('.moving-self').removeClass('moving-self');
                    $(event.target).addClass('moving-self');
                },
                out: function(event, ui) {
                    $(event.target).removeClass('moving-self');
                },
                start: function(event, ui) {
                    self.tree().prepareForNodeDrag();
                    self.tree().raiseEvent('nodeDragStart', {node: ui.item.data('control'), draggingFrom: ui.item.parent().data('control')});
                },
                stop: function(event, ui) {
                    self.tree().endNodeDrag();
                    
                    var movedItem = ui.item.data('control');
                    var droppedToItem = ui.item.parent().data('control');  
                    
                    self.tree().raiseEvent('nodeDragEnd', {node: movedItem, droppedTo: droppedToItem, before: ui.item.next().data('control'), after: ui.item.prev().data('control')});

                    movedItem.parent().controls(movedItem.name(), null);
                    if(movedItem.parent().count() == 0) {
                        movedItem.parentNode().removeClass('expandable');
                        movedItem.parentNode().Collapse();
                    }
                    droppedToItem.controls(movedItem.name(), movedItem);
                    droppedToItem.parent().addClass('expandable');
                    movedItem.parent(droppedToItem);
                    
                }
            });
        }
        return this;
    },
    
    Add: function(name) {
        var item = new UI.Controls.TreeNode(name, this, this._tree);    
        if(Object.keys(this.controls()).length > 0)
            this.parent().addClass('expandable');
        else
            this.parent().removeClass('expandable');
        this.tree().nodesList().push(item);
        
        item.Render();
        
        if(this.tree().dragManager()) {
            this.tree().raiseEvent('elementsAdded', {elements: [item._element[0]]});
        }                                                                               
        return item;
    },
    
    AddEditable: function(nodeData, editComplete) {
        
        var newnode = this.Add('new-element').title('<input type="text" style="width: 100%;" value="" />').icon(nodeData.icon).tag({isnew: true});
        if(this.parent().tree)
            this.parent().Expand();
        
        var self = this;
        newnode._element.find('input').focus().blur(function() {
            if($(this).val()) {
                newnode.Dispose();                           
                editComplete.apply(self, [self.parent(), $(this).val()])
            }                 
            else {
                newnode.Dispose();
            }
        }).keydown(function(e) {
            if(e.keyCode == 13 || e.keyCode == 27) {
                if(e.keyCode == 27)
                    $(this).val('');
                $(this).blur();
                return false;
            }   
        }).ensureVisible();
        
    },
    
    count: function() {
        return Object.countKeys(this._controls);
    }, 
    
    each: function(callback) {
        
        $.map(this._controls, function(value, name) {
            callback.apply(value, [name, value]);
        });
        
    },
    
    Dispose: function() {
        
        this.parent().removeClass('expandable');
        
        Object.forEach(this.controls(), function(name, value) {
            value.Dispose();
        });
        
        // this.base();
        
    },
    
    tree: function() {
        return this._tree;
    },
    
    sortable: function() {
        var self = this;
        if(this._sortiable)
            return;
        this.container().sortable({
            start: function(event, ui) {
                self.tree().raiseEvent('nodeSortStart', {node: ui.item.data('control')});
            },
            stop: function(event, ui) {
                self.tree().raiseEvent('nodeSortEnd', {node: ui.item.data('control'), before: ui.item.next().data('control'), after: ui.item.prev().data('control')});
            }
        });
        this._sortiable = true;
    },

    Clear: function() {
        this.each(function(name, node) {
            node.Dispose();
        });
    },

    

    
});
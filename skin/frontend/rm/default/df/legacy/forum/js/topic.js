var _HELP_OBJ = {};
var DEL_ =  Class.create({

       initialize: function(_json, _html, _target, _isParent, _parent, _winParent)
       {
           this.json      =  _json   ? _json    : 0;
           this.target    =  _target ? _target  : 0;
           this.html      =  _html   ? _html    : 0;
           this.isPar     =  _isParent;
           this.parent    =  _parent;
           this.winParent = _winParent ? _winParent : 0;
           this.construct();
       },
       construct : function()
       {
           var el_ = {};
           if(!this.target) this.target = document.body;
           if(this.winParent == 1) doc = window.parent.document;
           else                    doc = document;
           if( this.json.___tagName)
           {
               el_ = doc.createElement(this.json.___tagName);
           }
           else
           {
               el_ = doc.createElement("div");
           }
           if(this.json)
           {
              var obj    = this.json;
              var _keys_ = Object.keys(this.json);
              _keys_.each(function(k)
              {
                 if(Object.isString(obj[k]) && k != '___tagName' && k!='___text')
                 {
                       if(obj[k].endsWith('}'))
                       {
                            eval('el_.'+k+'='+obj[k]);
                       }
                       else
                       {
                             eval('el_.'+k+'='+"'"+obj[k]+"'"+';');
                       }
                 }
                 
                 if(k == '___text')
                 {
                      text_ = document.createTextNode(obj[k]);
                      if(el_)
                      {
                         el_.appendChild(text_);
                      }
                 }
              });
                       this.target.appendChild(el_);
                       if(!this.isPar)
                       {
                           this.parent = el_;
                       }
                       if(this.json.style)
                       {
                            obj        = this.json.style;
                            keys =  Object.keys(this.json.style);
                            keys.each(function(k)
                            {
                                   eval('el_.style.'+k+'='+"'"+obj[k]+"'"+';');
                            });
                       }
                       if(this.json.___childs)
                       {
                            obj        = this.json.___childs;
                            keys =  Object.keys(this.json.___childs);
                            keys.each(function(k)
                            {
                                   new DEL_(obj[k], '',el_, 1, this.parent, (this.winParent?this.winParent:'0'));
                            });
                       }

              return this.parent;
           }
           else
           {
                this.target.innerHTML = this.html;
                return this.target;
           }
       }
});

function debug( text )
{
   $('debug').innerHTML += text;
}

function evalScripts( _html )
{
	var js = 0;
	var re = new RegExp("\n|\r",'img');
	_html = _html.replace(re,'');
	re = new RegExp('<script.*?>(.*)</script>')
	var matches = _html.match(re);
	if(matches)
	{
		js = matches[1];
	}  
	return js;
}
    

var tBaloon     = false;
var JSON_BALOON = {
                        id:'_id_baloon',
                        className:'tBaloon',
                        style:{
                            position: 'absolute',
                            height:'30px'
                        }
                  };
var JSON_FRAME  = {
	id:'_id_frame',
	className:'tFrameBg',
	___tagName:'iframe',
	style:{
                            position: 'absolute',
                            width:'1px',
                            height:'1px'
    }
};                  
var doBaloon = Class.create(DEL_, {
      initialize: function(_obj, _url, _is_help, _text){
      	  if(_obj.is_created)
      	  {
			 return;		
		  }
          this.obj        = _obj;
          this.url        = _url;
          this.is_help = _is_help;
          this.responseTextServ = _text ? _text : false;
          this.register();
      },
      register : function()
      {
            this.reg = Event.observe(this.obj, 'mousemove', this.create.bindAsEventListener(this));
      },
      create  : function(event)
      {
      	    this.obj.is_created = this;
            if(this.baloon){ //do nothing
            }
            else
            {
                this.event                  = event;
		        this.coordEl                = Element.cumulativeOffset(this.obj);
		        this.demenEl                = Element.getDimensions(this.obj);
		        this.minTop                 = this.coordEl.top;
		        this.minLeft                = this.coordEl.left;
		        this.maxRight               = this.coordEl.left + this.demenEl.width;
		        this.maxBottom              = this.coordEl.top  + this.demenEl.height;
		        this.json       			= JSON_FRAME;
		        if(Prototype.Browser.IE)this.iframe                 = this.construct();
		        this.json       			= JSON_BALOON;
		        this.baloon                 = this.construct();
		        if(_HELP_OBJ[this.url])
		        {
					this.responseTextServ = _HELP_OBJ[this.url];
				}
		        if(!this.responseTextServ)
		        {
			        if(Object.isString(this.url) && this.url != '')
		            {
		                 new sendResponse(this.url, '', {t:this.baloon, m:'HTML'}, 0, this, 1);
		            }
		         }
		         else
		         {
		         		this.baloon.innerHTML = this.responseTextServ;
		                this.baloon.style.height = 'auto';
		                
						if(Prototype.Browser.IE)
						{
							$('_id_frame').style.height = $('_id_baloon').offsetHeight + 'px';	
						} 
		         }     
				 this.setAlign();
		         Event.stopObserving(this.obj, 'mousemove');
		         Event.observe(this.obj, 'mousemove', this.moveBaloon.bindAsEventListener(this));   
		         Event.observe(this.obj, 'mouseout' , this.hideBaloon.bindAsEventListener(this));
	        }
	  },
      setAlign : function()
      {
              if ( !this.event )
			  {
			        this.event = window.event;
			  }
				this.setBaloon(Event.pointerX(this.event), Event.pointerY(this.event));
              //this.baloon.style.top  = Event.pointerY(this.event) + 10 + 'px';
              //if(this.iframe)this.iframe.style.top  = Event.pointerY(this.event) + 10 + 'px';
              //this.baloon.style.left = Event.pointerX(this.event) + 10 + 'px';
              //if(this.iframe)this.iframe.style.left = Event.pointerX(this.event) + 10 + 'px';
      },
      moveBaloon : function(event)
      {
              if(!this.baloon) return;
			  xPos = Event.pointerX(event);
			  yPos = Event.pointerY(event);
			  this.setBaloon(xPos, yPos);
      },
      setBaloon  : function(xPos, yPos)
      {
		  var _heightWindow = document.viewport.getHeight() + document.viewport.getScrollOffsets().top;
		  var _baloonHeight = this.baloon.offsetHeight;
		  if(yPos + _baloonHeight > _heightWindow)
		  {
			  this.baloon.style.top  = yPos - _baloonHeight - 10 + "px";
			  if(this.iframe)this.iframe.style.top  = yPos - _baloonHeight - 10 + "px";
		  }
		  else
		  {
			  this.baloon.style.top  = yPos + 10 + "px";
			  if(this.iframe)this.iframe.style.top  = yPos + 10 + "px";
		  }
		  if(this.iframe)this.iframe.style.left = xPos + 10 + "px";
		  this.baloon.style.left = xPos + 10 + "px";
      },
      hideBaloon : function()
      {
              if(this.baloon)
              {
                    this.baloon.parentNode.removeChild( this.baloon );
                    if(this.iframe)this.iframe.parentNode.removeChild( this.iframe );
                    this.baloon = false;
                    Event.stopObserving(this.obj, 'mousemove'); 
                    Event.stopObserving(this.obj, 'mouseout');
                    this.register();
              }
      }
});
/*
* Section parallax effect
 */
(function(document,window){

  tenwebParallax = function(element,options){

    var defoultOptions = {
      vertical_scroll:{
        active: false,
        direction: 'up',
        speed: 4,
      },
      horizontal_scroll:{
        active: false,
        direction: 'right',
        speed: 4,
      },
      transparency:{
        active: false,
        direction: 'in',
        speed: 5,
      },
      blur:{
        active: false,
        direction: 'in',
        speed: 10,
      },
      scale:{
        active: false,
        direction: 'in',
        speed: 10,
      }
    }
    this.element = element;
    var curOptions = {};
    mergeOptions(options);
    this.layerDiv = createInnerDiv();
    this.options = curOptions;
    function mergeOptions(userOpt){
      // checking if options acceptable then set user def options else set def options
      curOptions = defoultOptions;
      if(typeof userOpt !== 'object')
        curOptions = defoultOptions;
      let optionsArray = Object.entries(userOpt);
      for(let i = 0; i < optionsArray.length;i++){
        if(typeof optionsArray[i] === 'object' && typeof defoultOptions[optionsArray[i][0]] === 'object'){
          if(typeof optionsArray[i][1] === 'object'){
            if(typeof optionsArray[i][1].active !== 'undefined' && ["on", "yes","On", "Yes", true].includes(optionsArray[i][1].active))
              curOptions[optionsArray[i][0]].active=true;
            if(typeof optionsArray[i][1].speed !== 'undefined' && 0<=optionsArray[i][1].speed<=10)
              curOptions[optionsArray[i][0]].speed=optionsArray[i][1].speed;
            if(typeof optionsArray[i][1].direction !== 'undefined' && ['in','out','up','down'].includes(optionsArray[i][1].direction))
              curOptions[optionsArray[i][0]].direction=optionsArray[i][1].direction;
          }
        }
      }
    }
    function createInnerDiv(){
      var conteinerDiv = document.createElement('div');
      conteinerDiv.classList.add("tenweb-elementor-scrolling-effects-container");
      var layerDiv = document.createElement('div');
      layerDiv.classList.add("tenweb-elementor-scrolling-effects-layer");

      var style = element.currentStyle || window.getComputedStyle(element, null);

      layerDiv.style.backgroundImage = style.backgroundImage;
      layerDiv.style.backgroundPosition = style.backgroundPosition;
      layerDiv.style.backgroundRepeat = style.backgroundRepeat;
      layerDiv.style.backgroundSize = style.backgroundSize;

      conteinerDiv.appendChild(layerDiv);
      element.prepend(conteinerDiv);
      return layerDiv;
    }
  }

  tenwebParallax.prototype = {
    vertical_transform:function(){
      if(this.options.vertical_scroll.active){
        this.layerDiv.style.height = (100 + this.options.vertical_scroll.speed * 100 / 10) + "%";
        if(this.isElementVisible()){
          var center = this.element.offsetHeight * this.options.vertical_scroll.speed / 2 / 10;
          var backgroundScrolled = - (window.scrollY + window.innerHeight - this.element.offsetTop) * this.options.vertical_scroll.speed / 4 / 10;
          if(this.options.vertical_scroll.direction == 'down')
            backgroundScrolled = - backgroundScrolled;
          return "translateY(calc(-" + center + "px + " + backgroundScrolled + "px))";
        }else{
          return '';
        }
      }
      return '';
    },
    horizontal_transform:function(){
      if(this.options.horizontal_scroll.active){
        this.layerDiv.style.width = (100 + this.options.horizontal_scroll.speed * 100 / 10) + "%";
        if(this.isElementVisible()){
          var backgroundScrolled =  (window.scrollY + window.innerHeight - this.element.offsetTop) * this.options.horizontal_scroll.speed/4  / 10;
          var center = this.element.offsetWidth * this.options.horizontal_scroll.speed/ 2 / 10;
          if(this.options.horizontal_scroll.direction == 'left')
            backgroundScrolled = - backgroundScrolled;
          return "translateX(calc(-" + center + "px + " + backgroundScrolled + "px))";
        }else{
          return '';
        }
      }
      return '';
    },
    transparency:function(){
      if(this.options.transparency.active){
        if(this.isElementVisible()){
          opacity_value = (window.scrollY + window.innerHeight - this.element.offsetTop) / (this.element.offsetHeight + window.innerHeight)
          opacity_value = opacity_value*(this.options.transparency.speed/10);
          if(this.options.transparency.direction == 'out')
            opacity_value = 1 - opacity_value;
          return opacity_value;
        }else{
          return '';
        }
      }
      return '';
    },
    blur:function(){
      if(this.options.blur.active){
        if(this.isElementVisible()){
          blur_value = (window.scrollY + window.innerHeight - this.element.offsetTop) / (this.element.offsetHeight + window.innerHeight)
          blur_value = blur_value * this.options.blur.speed;
          if(this.options.blur.direction == 'out')
            blur_value = 10 - blur_value;
          return "blur(" + blur_value + "px)";
        }else{
          return '';
        }
      }
      return '';
    },
    scale:function(){
      if(this.options.scale.active){
        if(this.isElementVisible()){
          scale_value = (window.scrollY + window.innerHeight - this.element.offsetTop) / (this.element.offsetHeight + window.innerHeight)
          scale_value = scale_value*(this.options.scale.speed)/10;
          scale_value = scale_value+1;
          if(this.options.scale.direction == 'out')
            scale_value = 2 - scale_value;
          return "scale(" + scale_value + ")";
        }else{
          return '';
        }
      }
      return '';
    },
    onScroll:function(){
      var transform_options = this.vertical_transform();
      transform_options += this.horizontal_transform();
      transform_options += this.scale();
      this.layerDiv.style.transform = transform_options;
      this.layerDiv.style.opacity = this.transparency();
      this.layerDiv.style.filter = this.blur();

    },
    onResize:function(){
      var transform_options = this.vertical_transform();
      transform_options += this.horizontal_transform();
      transform_options += this.scale();
      this.layerDiv.style.transform = transform_options;
      this.layerDiv.style.opacity = this.transparency();
      this.layerDiv.style.filter = this.blur();
    },
    changePosition:function(){

    },
    isElementVisible:function(){
      var ElementPositionInfo = this.element.getBoundingClientRect();
      if(ElementPositionInfo.top + ElementPositionInfo.height >= 0 && ElementPositionInfo.top <= (window.innerHeight || document.documentElement.clientHeight))
        return true;
      return false;
    },
    elementTopPosition:function(){
      return ;
    },
    elementBottomPosition:function(){
      return ;
    },

    addDisableBackgroundClass:function(){
      this.element.classList.add("tenweb-disable-background-image");
    },
    removeDisableBackgroundClass:function(){
      this.element.classList.remove("tenweb-disable-background-image");
    },
    start:function(){
      var self = this;
      this.onScroll = this.onScroll.bind(this);
      this.onResize = this.onResize.bind(this);
      this.addDisableBackgroundClass();
      window.addEventListener('scroll',this.onScroll);
      window.addEventListener('resize',this.onResize);
      self.onResize();
      return this;
    },
    destroy:function(){
      this.layerDiv.parentElement.remove();
      this.removeDisableBackgroundClass();
      window.removeEventListener('scroll',this.onScroll);
      window.removeEventListener('resize',this.onResize);
    }
  }

})(document,window);

var getBrowser, uaMatch;
getBrowser = function() {
    var a, b;
    a = uaMatch(navigator.userAgent);
    b = {};
    if (a.browser) {
        b[a.browser] = true;
        b.version = a.version;
    }
    if (b.chrome) {
        b.webkit = true;
    } else {
        if (b.webkit) {
          b.safari = true;
        }
    }
    return b;
};
uaMatch = function(b) {
    var a;
    b = b.toLowerCase();
    a = /(chrome)[ \/]([\w.]+)/.exec(b) || /(webkit)[ \/]([\w.]+)/.exec(b) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(b) || /(msie) ([\w.]+)/.exec(b) || b.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(b) || [];
    return {
        browser: a[1] || "",
        version: a[2] || "0"
    };
};
yOSON.browser= getBrowser();
/* Utils Js*/
var Cookie = {
    create: function(name,value,days) {
        if(days){
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }else{ var expires = ""; }
        document.cookie = name+"="+value+expires+"; path=/";
        return this;
    },
    read: function(name){
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++){
            var c = ca[i]; while(c.charAt(0)==' '){ c=c.substring(1,c.length); }
            if(c.indexOf(nameEQ)==0){ return c.substring(nameEQ.length,c.length); }
        } return null;
    },
    del: function(name){
        return this.create(name, "", -1);
    }
};
/* Utils */
var utils={
    loader:function(content,cond,table){
        if(typeof table=="undefined"){
            if(cond){
                content.css("position","relative");
                content.append("<div class='loader'><span></span></div>");
            }else{
                content.find(".loader").remove();
            }
        }else{
            if(cond){
                var hash=utils.unique(),
                    pos=content.offset(),
                    styl="height:"+content.outerHeight(true)+"px;width:"+content.outerWidth(true)+"px;top:"+pos.top+"px;left:"+pos.left+"px";
                    ele=$("<div />",{"id":hash,"class":"loader","style":"position:absolute;"+styl});
                ele.append("<span></span>");
                $("body").append(ele);
                return hash;
            }else{
                content.remove();
            }
        }
    },
    "vLength":function(input,len){
        $(input).bind("keyup",function(e){
            var _this=$(this),
                valor=_this.val();
            if(valor.length>len){
                _this.val(valor.substr(0,len));
            }
        });
        $(input).bind("paste",function(){
            var _this=$(this);
            setTimeout(function(){
                var valor=_this.val();
                if(valor.length>len){
                    _this.val(valor.substr(0,len));
                }
            },300);
        });
    },
    block:function(content,cond){
        if(cond){
            if(content.find(".ublock").length==0){
                content.css("position","relative");
                content.append("<div class='ublock'></div>");
            }
        }else{
            content.find(".ublock").remove();
        }
    },
    "serializeJson":function(form){
        var data=form.serializeArray(),
            json={},
            tmp;
        for(var i=0;i<data.length;i++){
            tmp=data[i];
            json[tmp.name]=tmp.value;
        }
        return json;
    },
    "unique":function(){
        return Math.random().toString(36).substr(2);
    }
};
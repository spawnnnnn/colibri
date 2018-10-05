/**
 * Version: 1.0 Alpha-1 
 * Build Date: 13-Nov-2007
 * Copyright (c) 2006-2007, Coolite Inc. (http://www.coolite.com/). All rights reserved.
 * License: Licensed under The MIT License. See license.txt and http://www.datejs.com/license/. 
 * Website: http://www.datejs.com/ or http://www.coolite.com/datejs/
 */
Date.CultureInfo={name:"en-US",englishName:"English (United States)",nativeName:"English (United States)",dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],abbreviatedDayNames:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],shortestDayNames:["Su","Mo","Tu","We","Th","Fr","Sa"],firstLetterDayNames:["S","M","T","W","T","F","S"],monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],abbreviatedMonthNames:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],amDesignator:"AM",pmDesignator:"PM",firstDayOfWeek:0,twoDigitYearMax:2029,dateElementOrder:"mdy",formatPatterns:{shortDate:"M/d/yyyy",longDate:"dddd, MMMM dd, yyyy",shortTime:"h:mm tt",longTime:"h:mm:ss tt",fullDateTime:"dddd, MMMM dd, yyyy h:mm:ss tt",sortableDateTime:"yyyy-MM-ddTHH:mm:ss",universalSortableDateTime:"yyyy-MM-dd HH:mm:ssZ",rfc1123:"ddd, dd MMM yyyy HH:mm:ss GMT",monthDay:"MMMM dd",yearMonth:"MMMM, yyyy"},regexPatterns:{jan:/^jan(uary)?/i,feb:/^feb(ruary)?/i,mar:/^mar(ch)?/i,apr:/^apr(il)?/i,may:/^may/i,jun:/^jun(e)?/i,jul:/^jul(y)?/i,aug:/^aug(ust)?/i,sep:/^sep(t(ember)?)?/i,oct:/^oct(ober)?/i,nov:/^nov(ember)?/i,dec:/^dec(ember)?/i,sun:/^su(n(day)?)?/i,mon:/^mo(n(day)?)?/i,tue:/^tu(e(s(day)?)?)?/i,wed:/^we(d(nesday)?)?/i,thu:/^th(u(r(s(day)?)?)?)?/i,fri:/^fr(i(day)?)?/i,sat:/^sa(t(urday)?)?/i,future:/^next/i,past:/^last|past|prev(ious)?/i,add:/^(\+|after|from)/i,subtract:/^(\-|before|ago)/i,yesterday:/^yesterday/i,today:/^t(oday)?/i,tomorrow:/^tomorrow/i,now:/^n(ow)?/i,millisecond:/^ms|milli(second)?s?/i,second:/^sec(ond)?s?/i,minute:/^min(ute)?s?/i,hour:/^h(ou)?rs?/i,week:/^w(ee)?k/i,month:/^m(o(nth)?s?)?/i,day:/^d(ays?)?/i,year:/^y((ea)?rs?)?/i,shortMeridian:/^(a|p)/i,longMeridian:/^(a\.?m?\.?|p\.?m?\.?)/i,timezone:/^((e(s|d)t|c(s|d)t|m(s|d)t|p(s|d)t)|((gmt)?\s*(\+|\-)\s*\d\d\d\d?)|gmt)/i,ordinalSuffix:/^\s*(st|nd|rd|th)/i,timeContext:/^\s*(\:|a|p)/i},abbreviatedTimeZoneStandard:{GMT:"-000",EST:"-0400",CST:"-0500",MST:"-0600",PST:"-0700"},abbreviatedTimeZoneDST:{GMT:"-000",EDT:"-0500",CDT:"-0600",MDT:"-0700",PDT:"-0800"}};
Date.getMonthNumberFromName=function(name){var n=Date.CultureInfo.monthNames,m=Date.CultureInfo.abbreviatedMonthNames,s=name.toLowerCase();for(var i=0;i<n.length;i++){if(n[i].toLowerCase()==s||m[i].toLowerCase()==s){return i;}}
return-1;};Date.getDayNumberFromName=function(name){var n=Date.CultureInfo.dayNames,m=Date.CultureInfo.abbreviatedDayNames,o=Date.CultureInfo.shortestDayNames,s=name.toLowerCase();for(var i=0;i<n.length;i++){if(n[i].toLowerCase()==s||m[i].toLowerCase()==s){return i;}}
return-1;};Date.isLeapYear=function(year){return(((year%4===0)&&(year%100!==0))||(year%400===0));};Date.getDaysInMonth=function(year,month){return[31,(Date.isLeapYear(year)?29:28),31,30,31,30,31,31,30,31,30,31][month];};Date.getTimezoneOffset=function(s,dst){return(dst||false)?Date.CultureInfo.abbreviatedTimeZoneDST[s.toUpperCase()]:Date.CultureInfo.abbreviatedTimeZoneStandard[s.toUpperCase()];};Date.getTimezoneAbbreviation=function(offset,dst){var n=(dst||false)?Date.CultureInfo.abbreviatedTimeZoneDST:Date.CultureInfo.abbreviatedTimeZoneStandard,p;for(p in n){if(n[p]===offset){return p;}}
return null;};Date.prototype.clone=function(){return new Date(this.getTime());};Date.prototype.compareTo=function(date){if(isNaN(this)){throw new Error(this);}
if(date instanceof Date&&!isNaN(date)){return(this>date)?1:(this<date)?-1:0;}else{throw new TypeError(date);}};Date.prototype.equals=function(date){return(this.compareTo(date)===0);};Date.prototype.between=function(start,end){var t=this.getTime();return t>=start.getTime()&&t<=end.getTime();};Date.prototype.addMilliseconds=function(value){this.setMilliseconds(this.getMilliseconds()+value);return this;};Date.prototype.addSeconds=function(value){return this.addMilliseconds(value*1000);};Date.prototype.addMinutes=function(value){return this.addMilliseconds(value*60000);};Date.prototype.addHours=function(value){return this.addMilliseconds(value*3600000);};Date.prototype.addDays=function(value){return this.addMilliseconds(value*86400000);};Date.prototype.addWeeks=function(value){return this.addMilliseconds(value*604800000);};Date.prototype.addMonths=function(value){var n=this.getDate();this.setDate(1);this.setMonth(this.getMonth()+value);this.setDate(Math.min(n,this.getDaysInMonth()));return this;};Date.prototype.addYears=function(value){return this.addMonths(value*12);};Date.prototype.add=function(config){if(typeof config=="number"){this._orient=config;return this;}
var x=config;if(x.millisecond||x.milliseconds){this.addMilliseconds(x.millisecond||x.milliseconds);}
if(x.second||x.seconds){this.addSeconds(x.second||x.seconds);}
if(x.minute||x.minutes){this.addMinutes(x.minute||x.minutes);}
if(x.hour||x.hours){this.addHours(x.hour||x.hours);}
if(x.month||x.months){this.addMonths(x.month||x.months);}
if(x.year||x.years){this.addYears(x.year||x.years);}
if(x.day||x.days){this.addDays(x.day||x.days);}
return this;};Date._validate=function(value,min,max,name){if(typeof value!="number"){throw new TypeError(value+" is not a Number.");}else if(value<min||value>max){throw new RangeError(value+" is not a valid value for "+name+".");}
return true;};Date.validateMillisecond=function(n){return Date._validate(n,0,999,"milliseconds");};Date.validateSecond=function(n){return Date._validate(n,0,59,"seconds");};Date.validateMinute=function(n){return Date._validate(n,0,59,"minutes");};Date.validateHour=function(n){return Date._validate(n,0,23,"hours");};Date.validateDay=function(n,year,month){return Date._validate(n,1,Date.getDaysInMonth(year,month),"days");};Date.validateMonth=function(n){return Date._validate(n,0,11,"months");};Date.validateYear=function(n){return Date._validate(n,1,9999,"seconds");};Date.prototype.set=function(config){var x=config;if(!x.millisecond&&x.millisecond!==0){x.millisecond=-1;}
if(!x.second&&x.second!==0){x.second=-1;}
if(!x.minute&&x.minute!==0){x.minute=-1;}
if(!x.hour&&x.hour!==0){x.hour=-1;}
if(!x.day&&x.day!==0){x.day=-1;}
if(!x.month&&x.month!==0){x.month=-1;}
if(!x.year&&x.year!==0){x.year=-1;}
if(x.millisecond!=-1&&Date.validateMillisecond(x.millisecond)){this.addMilliseconds(x.millisecond-this.getMilliseconds());}
if(x.second!=-1&&Date.validateSecond(x.second)){this.addSeconds(x.second-this.getSeconds());}
if(x.minute!=-1&&Date.validateMinute(x.minute)){this.addMinutes(x.minute-this.getMinutes());}
if(x.hour!=-1&&Date.validateHour(x.hour)){this.addHours(x.hour-this.getHours());}
if(x.month!==-1&&Date.validateMonth(x.month)){this.addMonths(x.month-this.getMonth());}
if(x.year!=-1&&Date.validateYear(x.year)){this.addYears(x.year-this.getFullYear());}
if(x.day!=-1&&Date.validateDay(x.day,this.getFullYear(),this.getMonth())){this.addDays(x.day-this.getDate());}
if(x.timezone){this.setTimezone(x.timezone);}
if(x.timezoneOffset){this.setTimezoneOffset(x.timezoneOffset);}
return this;};Date.prototype.clearTime=function(){this.setHours(0);this.setMinutes(0);this.setSeconds(0);this.setMilliseconds(0);return this;};Date.prototype.isLeapYear=function(){var y=this.getFullYear();return(((y%4===0)&&(y%100!==0))||(y%400===0));};Date.prototype.isWeekday=function(){return!(this.is().sat()||this.is().sun());};Date.prototype.getDaysInMonth=function(){return Date.getDaysInMonth(this.getFullYear(),this.getMonth());};Date.prototype.moveToFirstDayOfMonth=function(){return this.set({day:1});};Date.prototype.moveToLastDayOfMonth=function(){return this.set({day:this.getDaysInMonth()});};Date.prototype.moveToDayOfWeek=function(day,orient){var diff=(day-this.getDay()+7*(orient||+1))%7;return this.addDays((diff===0)?diff+=7*(orient||+1):diff);};Date.prototype.moveToMonth=function(month,orient){var diff=(month-this.getMonth()+12*(orient||+1))%12;return this.addMonths((diff===0)?diff+=12*(orient||+1):diff);};Date.prototype.getDayOfYear=function(){return Math.floor((this-new Date(this.getFullYear(),0,1))/86400000);};Date.prototype.getWeekOfYear=function(firstDayOfWeek){var y=this.getFullYear(),m=this.getMonth(),d=this.getDate();var dow=firstDayOfWeek||Date.CultureInfo.firstDayOfWeek;var offset=7+1-new Date(y,0,1).getDay();if(offset==8){offset=1;}
var daynum=((Date.UTC(y,m,d,0,0,0)-Date.UTC(y,0,1,0,0,0))/86400000)+1;var w=Math.floor((daynum-offset+7)/7);if(w===dow){y--;var prevOffset=7+1-new Date(y,0,1).getDay();if(prevOffset==2||prevOffset==8){w=53;}else{w=52;}}
return w;};Date.prototype.isDST=function(){console.log('isDST');return this.toString().match(/(E|C|M|P)(S|D)T/)[2]=="D";};Date.prototype.getTimezone=function(){return Date.getTimezoneAbbreviation(this.getUTCOffset,this.isDST());};Date.prototype.setTimezoneOffset=function(s){var here=this.getTimezoneOffset(),there=Number(s)*-6/10;this.addMinutes(there-here);return this;};Date.prototype.setTimezone=function(s){return this.setTimezoneOffset(Date.getTimezoneOffset(s));};Date.prototype.getUTCOffset=function(){var n=this.getTimezoneOffset()*-10/6,r;if(n<0){r=(n-10000).toString();return r[0]+r.substr(2);}else{r=(n+10000).toString();return"+"+r.substr(1);}};Date.prototype.getDayName=function(abbrev){return abbrev?Date.CultureInfo.abbreviatedDayNames[this.getDay()]:Date.CultureInfo.dayNames[this.getDay()];};Date.prototype.getMonthName=function(abbrev){return abbrev?Date.CultureInfo.abbreviatedMonthNames[this.getMonth()]:Date.CultureInfo.monthNames[this.getMonth()];};Date.prototype._toString=Date.prototype.toString;Date.prototype.toString=function(format){var self=this;var p=function p(s){return(s.toString().length==1)?"0"+s:s;};return format?format.replace(/dd?d?d?|MM?M?M?|yy?y?y?|hh?|HH?|mm?|ss?|tt?|zz?z?/g,function(format){switch(format){case"hh":return p(self.getHours()<13?self.getHours():(self.getHours()-12));case"h":return self.getHours()<13?self.getHours():(self.getHours()-12);case"HH":return p(self.getHours());case"H":return self.getHours();case"mm":return p(self.getMinutes());case"m":return self.getMinutes();case"ss":return p(self.getSeconds());case"s":return self.getSeconds();case"yyyy":return self.getFullYear();case"yy":return self.getFullYear().toString().substring(2,4);case"dddd":return self.getDayName();case"ddd":return self.getDayName(true);case"dd":return p(self.getDate());case"d":return self.getDate().toString();case"MMMM":return self.getMonthName();case"MMM":return self.getMonthName(true);case"MM":return p((self.getMonth()+1));case"M":return self.getMonth()+1;case"t":return self.getHours()<12?Date.CultureInfo.amDesignator.substring(0,1):Date.CultureInfo.pmDesignator.substring(0,1);case"tt":return self.getHours()<12?Date.CultureInfo.amDesignator:Date.CultureInfo.pmDesignator;case"zzz":case"zz":case"z":return"";}}):this._toString();};
Date.now=function(){return new Date();};Date.today=function(){return Date.now().clearTime();};Date.prototype._orient=+1;Date.prototype.next=function(){this._orient=+1;return this;};Date.prototype.last=Date.prototype.prev=Date.prototype.previous=function(){this._orient=-1;return this;};Date.prototype._is=false;Date.prototype.is=function(){this._is=true;return this;};Number.prototype._dateElement="day";Number.prototype.fromNow=function(){var c={};c[this._dateElement]=this;return Date.now().add(c);};Number.prototype.ago=function(){var c={};c[this._dateElement]=this*-1;return Date.now().add(c);};(function(){var $D=Date.prototype,$N=Number.prototype;var dx=("sunday monday tuesday wednesday thursday friday saturday").split(/\s/),mx=("january february march april may june july august september october november december").split(/\s/),px=("Millisecond Second Minute Hour Day Week Month Year").split(/\s/),de;var df=function(n){return function(){if(this._is){this._is=false;return this.getDay()==n;}
return this.moveToDayOfWeek(n,this._orient);};};for(var i=0;i<dx.length;i++){$D[dx[i]]=$D[dx[i].substring(0,3)]=df(i);}
var mf=function(n){return function(){if(this._is){this._is=false;return this.getMonth()===n;}
return this.moveToMonth(n,this._orient);};};for(var j=0;j<mx.length;j++){$D[mx[j]]=$D[mx[j].substring(0,3)]=mf(j);}
var ef=function(j){return function(){if(j.substring(j.length-1)!="s"){j+="s";}
return this["add"+j](this._orient);};};var nf=function(n){return function(){this._dateElement=n;return this;};};for(var k=0;k<px.length;k++){de=px[k].toLowerCase();$D[de]=$D[de+"s"]=ef(px[k]);$N[de]=$N[de+"s"]=nf(de);}}());Date.prototype.toJSONString=function(){return this.toString("yyyy-MM-ddThh:mm:ssZ");};Date.prototype.toShortDateString=function(){return this.toString(Date.CultureInfo.formatPatterns.shortDatePattern);};Date.prototype.toLongDateString=function(){return this.toString(Date.CultureInfo.formatPatterns.longDatePattern);};Date.prototype.toShortTimeString=function(){return this.toString(Date.CultureInfo.formatPatterns.shortTimePattern);};Date.prototype.toLongTimeString=function(){return this.toString(Date.CultureInfo.formatPatterns.longTimePattern);};Date.prototype.getOrdinal=function(){switch(this.getDate()){case 1:case 21:case 31:return"st";case 2:case 22:return"nd";case 3:case 23:return"rd";default:return"th";}};
(function(){Date.Parsing={Exception:function(s){this.message="Parse error at '"+s.substring(0,10)+" ...'";}};var $P=Date.Parsing;var _=$P.Operators={rtoken:function(r){return function(s){var mx=s.match(r);if(mx){return([mx[0],s.substring(mx[0].length)]);}else{throw new $P.Exception(s);}};},token:function(s){return function(s){return _.rtoken(new RegExp("^\s*"+s+"\s*"))(s);};},stoken:function(s){return _.rtoken(new RegExp("^"+s));},until:function(p){return function(s){var qx=[],rx=null;while(s.length){try{rx=p.call(this,s);}catch(e){qx.push(rx[0]);s=rx[1];continue;}
break;}
return[qx,s];};},many:function(p){return function(s){var rx=[],r=null;while(s.length){try{r=p.call(this,s);}catch(e){return[rx,s];}
rx.push(r[0]);s=r[1];}
return[rx,s];};},optional:function(p){return function(s){var r=null;try{r=p.call(this,s);}catch(e){return[null,s];}
return[r[0],r[1]];};},not:function(p){return function(s){try{p.call(this,s);}catch(e){return[null,s];}
throw new $P.Exception(s);};},ignore:function(p){return p?function(s){var r=null;r=p.call(this,s);return[null,r[1]];}:null;},product:function(){var px=arguments[0],qx=Array.prototype.slice.call(arguments,1),rx=[];for(var i=0;i<px.length;i++){rx.push(_.each(px[i],qx));}
return rx;},cache:function(rule){var cache={},r=null;return function(s){try{r=cache[s]=(cache[s]||rule.call(this,s));}catch(e){r=cache[s]=e;}
if(r instanceof $P.Exception){throw r;}else{return r;}};},any:function(){var px=arguments;return function(s){var r=null;for(var i=0;i<px.length;i++){if(px[i]==null){continue;}
try{r=(px[i].call(this,s));}catch(e){r=null;}
if(r){return r;}}
throw new $P.Exception(s);};},each:function(){var px=arguments;return function(s){var rx=[],r=null;for(var i=0;i<px.length;i++){if(px[i]==null){continue;}
try{r=(px[i].call(this,s));}catch(e){throw new $P.Exception(s);}
rx.push(r[0]);s=r[1];}
return[rx,s];};},all:function(){var px=arguments,_=_;return _.each(_.optional(px));},sequence:function(px,d,c){d=d||_.rtoken(/^\s*/);c=c||null;if(px.length==1){return px[0];}
return function(s){var r=null,q=null;var rx=[];for(var i=0;i<px.length;i++){try{r=px[i].call(this,s);}catch(e){break;}
rx.push(r[0]);try{q=d.call(this,r[1]);}catch(ex){q=null;break;}
s=q[1];}
if(!r){throw new $P.Exception(s);}
if(q){throw new $P.Exception(q[1]);}
if(c){try{r=c.call(this,r[1]);}catch(ey){throw new $P.Exception(r[1]);}}
return[rx,(r?r[1]:s)];};},between:function(d1,p,d2){d2=d2||d1;var _fn=_.each(_.ignore(d1),p,_.ignore(d2));return function(s){var rx=_fn.call(this,s);return[[rx[0][0],r[0][2]],rx[1]];};},list:function(p,d,c){d=d||_.rtoken(/^\s*/);c=c||null;return(p instanceof Array?_.each(_.product(p.slice(0,-1),_.ignore(d)),p.slice(-1),_.ignore(c)):_.each(_.many(_.each(p,_.ignore(d))),px,_.ignore(c)));},set:function(px,d,c){d=d||_.rtoken(/^\s*/);c=c||null;return function(s){var r=null,p=null,q=null,rx=null,best=[[],s],last=false;for(var i=0;i<px.length;i++){q=null;p=null;r=null;last=(px.length==1);try{r=px[i].call(this,s);}catch(e){continue;}
rx=[[r[0]],r[1]];if(r[1].length>0&&!last){try{q=d.call(this,r[1]);}catch(ex){last=true;}}else{last=true;}
if(!last&&q[1].length===0){last=true;}
if(!last){var qx=[];for(var j=0;j<px.length;j++){if(i!=j){qx.push(px[j]);}}
p=_.set(qx,d).call(this,q[1]);if(p[0].length>0){rx[0]=rx[0].concat(p[0]);rx[1]=p[1];}}
if(rx[1].length<best[1].length){best=rx;}
if(best[1].length===0){break;}}
if(best[0].length===0){return best;}
if(c){try{q=c.call(this,best[1]);}catch(ey){throw new $P.Exception(best[1]);}
best[1]=q[1];}
return best;};},forward:function(gr,fname){return function(s){return gr[fname].call(this,s);};},replace:function(rule,repl){return function(s){var r=rule.call(this,s);return[repl,r[1]];};},process:function(rule,fn){return function(s){var r=rule.call(this,s);return[fn.call(this,r[0]),r[1]];};},min:function(min,rule){return function(s){var rx=rule.call(this,s);if(rx[0].length<min){throw new $P.Exception(s);}
return rx;};}};var _generator=function(op){return function(){var args=null,rx=[];if(arguments.length>1){args=Array.prototype.slice.call(arguments);}else if(arguments[0]instanceof Array){args=arguments[0];}
if(args){for(var i=0,px=args.shift();i<px.length;i++){args.unshift(px[i]);rx.push(op.apply(null,args));args.shift();return rx;}}else{return op.apply(null,arguments);}};};var gx="optional not ignore cache".split(/\s/);for(var i=0;i<gx.length;i++){_[gx[i]]=_generator(_[gx[i]]);}
var _vector=function(op){return function(){if(arguments[0]instanceof Array){return op.apply(null,arguments[0]);}else{return op.apply(null,arguments);}};};var vx="each any all".split(/\s/);for(var j=0;j<vx.length;j++){_[vx[j]]=_vector(_[vx[j]]);}}());(function(){var flattenAndCompact=function(ax){var rx=[];for(var i=0;i<ax.length;i++){if(ax[i]instanceof Array){rx=rx.concat(flattenAndCompact(ax[i]));}else{if(ax[i]){rx.push(ax[i]);}}}
return rx;};Date.Grammar={};Date.Translator={hour:function(s){return function(){this.hour=Number(s);};},minute:function(s){return function(){this.minute=Number(s);};},second:function(s){return function(){this.second=Number(s);};},meridian:function(s){return function(){this.meridian=s.slice(0,1).toLowerCase();};},timezone:function(s){return function(){var n=s.replace(/[^\d\+\-]/g,"");if(n.length){this.timezoneOffset=Number(n);}else{this.timezone=s.toLowerCase();}};},day:function(x){var s=x[0];return function(){this.day=Number(s.match(/\d+/)[0]);};},month:function(s){return function(){this.month=((s.length==3)?Date.getMonthNumberFromName(s):(Number(s)-1));};},year:function(s){return function(){var n=Number(s);this.year=((s.length>2)?n:(n+(((n+2000)<Date.CultureInfo.twoDigitYearMax)?2000:1900)));};},rday:function(s){return function(){switch(s){case"yesterday":this.days=-1;break;case"tomorrow":this.days=1;break;case"today":this.days=0;break;case"now":this.days=0;this.now=true;break;}};},finishExact:function(x){x=(x instanceof Array)?x:[x];var now=new Date();this.year=now.getFullYear();this.month=now.getMonth();this.day=1;this.hour=0;this.minute=0;this.second=0;for(var i=0;i<x.length;i++){if(x[i]){x[i].call(this);}}
this.hour=(this.meridian=="p"&&this.hour<13)?this.hour+12:this.hour;if(this.day>Date.getDaysInMonth(this.year,this.month)){throw new RangeError(this.day+" is not a valid value for days.");}
var r=new Date(this.year,this.month,this.day,this.hour,this.minute,this.second);if(this.timezone){r.set({timezone:this.timezone});}else if(this.timezoneOffset){r.set({timezoneOffset:this.timezoneOffset});}
return r;},finish:function(x){x=(x instanceof Array)?flattenAndCompact(x):[x];if(x.length===0){return null;}
for(var i=0;i<x.length;i++){if(typeof x[i]=="function"){x[i].call(this);}}
if(this.now){return new Date();}
var today=Date.today();var method=null;var expression=!!(this.days!=null||this.orient||this.operator);if(expression){var gap,mod,orient;orient=((this.orient=="past"||this.operator=="subtract")?-1:1);if(this.weekday){this.unit="day";gap=(Date.getDayNumberFromName(this.weekday)-today.getDay());mod=7;this.days=gap?((gap+(orient*mod))%mod):(orient*mod);}
if(this.month){this.unit="month";gap=(this.month-today.getMonth());mod=12;this.months=gap?((gap+(orient*mod))%mod):(orient*mod);this.month=null;}
if(!this.unit){this.unit="day";}
if(this[this.unit+"s"]==null||this.operator!=null){if(!this.value){this.value=1;}
if(this.unit=="week"){this.unit="day";this.value=this.value*7;}
this[this.unit+"s"]=this.value*orient;}
return today.add(this);}else{if(this.meridian&&this.hour){this.hour=(this.hour<13&&this.meridian=="p")?this.hour+12:this.hour;}
if(this.weekday&&!this.day){this.day=(today.addDays((Date.getDayNumberFromName(this.weekday)-today.getDay()))).getDate();}
if(this.month&&!this.day){this.day=1;}
return today.set(this);}}};var _=Date.Parsing.Operators,g=Date.Grammar,t=Date.Translator,_fn;g.datePartDelimiter=_.rtoken(/^([\s\-\.\,\/\x27]+)/);g.timePartDelimiter=_.stoken(":");g.whiteSpace=_.rtoken(/^\s*/);g.generalDelimiter=_.rtoken(/^(([\s\,]|at|on)+)/);var _C={};g.ctoken=function(keys){var fn=_C[keys];if(!fn){var c=Date.CultureInfo.regexPatterns;var kx=keys.split(/\s+/),px=[];for(var i=0;i<kx.length;i++){px.push(_.replace(_.rtoken(c[kx[i]]),kx[i]));}
fn=_C[keys]=_.any.apply(null,px);}
return fn;};g.ctoken2=function(key){return _.rtoken(Date.CultureInfo.regexPatterns[key]);};g.h=_.cache(_.process(_.rtoken(/^(0[0-9]|1[0-2]|[1-9])/),t.hour));g.hh=_.cache(_.process(_.rtoken(/^(0[0-9]|1[0-2])/),t.hour));g.H=_.cache(_.process(_.rtoken(/^([0-1][0-9]|2[0-3]|[0-9])/),t.hour));g.HH=_.cache(_.process(_.rtoken(/^([0-1][0-9]|2[0-3])/),t.hour));g.m=_.cache(_.process(_.rtoken(/^([0-5][0-9]|[0-9])/),t.minute));g.mm=_.cache(_.process(_.rtoken(/^[0-5][0-9]/),t.minute));g.s=_.cache(_.process(_.rtoken(/^([0-5][0-9]|[0-9])/),t.second));g.ss=_.cache(_.process(_.rtoken(/^[0-5][0-9]/),t.second));g.hms=_.cache(_.sequence([g.H,g.mm,g.ss],g.timePartDelimiter));g.t=_.cache(_.process(g.ctoken2("shortMeridian"),t.meridian));g.tt=_.cache(_.process(g.ctoken2("longMeridian"),t.meridian));g.z=_.cache(_.process(_.rtoken(/^(\+|\-)?\s*\d\d\d\d?/),t.timezone));g.zz=_.cache(_.process(_.rtoken(/^(\+|\-)\s*\d\d\d\d/),t.timezone));g.zzz=_.cache(_.process(g.ctoken2("timezone"),t.timezone));g.timeSuffix=_.each(_.ignore(g.whiteSpace),_.set([g.tt,g.zzz]));g.time=_.each(_.optional(_.ignore(_.stoken("T"))),g.hms,g.timeSuffix);g.d=_.cache(_.process(_.each(_.rtoken(/^([0-2]\d|3[0-1]|\d)/),_.optional(g.ctoken2("ordinalSuffix"))),t.day));g.dd=_.cache(_.process(_.each(_.rtoken(/^([0-2]\d|3[0-1])/),_.optional(g.ctoken2("ordinalSuffix"))),t.day));g.ddd=g.dddd=_.cache(_.process(g.ctoken("sun mon tue wed thu fri sat"),function(s){return function(){this.weekday=s;};}));g.M=_.cache(_.process(_.rtoken(/^(1[0-2]|0\d|\d)/),t.month));g.MM=_.cache(_.process(_.rtoken(/^(1[0-2]|0\d)/),t.month));g.MMM=g.MMMM=_.cache(_.process(g.ctoken("jan feb mar apr may jun jul aug sep oct nov dec"),t.month));g.y=_.cache(_.process(_.rtoken(/^(\d\d?)/),t.year));g.yy=_.cache(_.process(_.rtoken(/^(\d\d)/),t.year));g.yyy=_.cache(_.process(_.rtoken(/^(\d\d?\d?\d?)/),t.year));g.yyyy=_.cache(_.process(_.rtoken(/^(\d\d\d\d)/),t.year));_fn=function(){return _.each(_.any.apply(null,arguments),_.not(g.ctoken2("timeContext")));};g.day=_fn(g.d,g.dd);g.month=_fn(g.M,g.MMM);g.year=_fn(g.yyyy,g.yy);g.orientation=_.process(g.ctoken("past future"),function(s){return function(){this.orient=s;};});g.operator=_.process(g.ctoken("add subtract"),function(s){return function(){this.operator=s;};});g.rday=_.process(g.ctoken("yesterday tomorrow today now"),t.rday);g.unit=_.process(g.ctoken("minute hour day week month year"),function(s){return function(){this.unit=s;};});g.value=_.process(_.rtoken(/^\d\d?(st|nd|rd|th)?/),function(s){return function(){this.value=s.replace(/\D/g,"");};});g.expression=_.set([g.rday,g.operator,g.value,g.unit,g.orientation,g.ddd,g.MMM]);_fn=function(){return _.set(arguments,g.datePartDelimiter);};g.mdy=_fn(g.ddd,g.month,g.day,g.year);g.ymd=_fn(g.ddd,g.year,g.month,g.day);g.dmy=_fn(g.ddd,g.day,g.month,g.year);g.date=function(s){return((g[Date.CultureInfo.dateElementOrder]||g.mdy).call(this,s));};g.format=_.process(_.many(_.any(_.process(_.rtoken(/^(dd?d?d?|MM?M?M?|yy?y?y?|hh?|HH?|mm?|ss?|tt?|zz?z?)/),function(fmt){if(g[fmt]){return g[fmt];}else{throw Date.Parsing.Exception(fmt);}}),_.process(_.rtoken(/^[^dMyhHmstz]+/),function(s){return _.ignore(_.stoken(s));}))),function(rules){return _.process(_.each.apply(null,rules),t.finishExact);});var _F={};var _get=function(f){return _F[f]=(_F[f]||g.format(f)[0]);};g.formats=function(fx){if(fx instanceof Array){var rx=[];for(var i=0;i<fx.length;i++){rx.push(_get(fx[i]));}
return _.any.apply(null,rx);}else{return _get(fx);}};g._formats=g.formats(["yyyy-MM-ddTHH:mm:ss","ddd, MMM dd, yyyy H:mm:ss tt","ddd MMM d yyyy HH:mm:ss zzz","d"]);g._start=_.process(_.set([g.date,g.time,g.expression],g.generalDelimiter,g.whiteSpace),t.finish);g.start=function(s){try{var r=g._formats.call({},s);if(r[1].length===0){return r;}}catch(e){}
return g._start.call({},s);};}());Date._parse=Date.parse;Date.parse=function(s){var r=null;if(!s){return null;}
try{r=Date.Grammar.start.call({},s);}catch(e){return null;}
return((r[1].length===0)?r[0]:null);};Date.getParseFunction=function(fx){var fn=Date.Grammar.formats(fx);return function(s){var r=null;try{r=fn.call({},s);}catch(e){return null;}
return((r[1].length===0)?r[0]:null);};};Date.parseExact=function(s,fx){return Date.getParseFunction(fx)(s);};

function isObject(o) { return typeof(o) == 'object'; }
function isArray(o) { if (o.constructor.toString().indexOf("Array") == -1) return false; return true; }

function toArray(o) { var arr = []; for( var i in o ) { if (o.hasOwnProperty(i)){ arr.push(o[i]); } } return arr; }
function toObject(arr) { var o = {}; for(var k in arr) { o[k] = arr[k]; } return o; }

function isChildOf(el, parent) {
    return parent.has(el).length > 0;
}

/* Array prototype expansion */
//Array.prototype.indexOf = function(value) { for(var i=0; i<this.length; i++) { if(this[i] == value) return i; }; return -1; }
//Array.prototype.create = function (initialSize) { for (var i = 0; i < initialSize; i++) { this[i] = ""; }; return this; }

Array.prototype.unique = function() { return this.filter(function(v, i, a){ return a.indexOf(v) === i; }); }
Array.prototype.merge = function(ar) {
    var self = this;
    $(ar).each(function(i, o) {
        self.push(o);
    });
    return this;
}
Array.prototype.part = function(e) {
    var self = this;
    var r = [];
    $(this).each(function(i, o) {
        if(eval(e))
            r.push(o);
    });
    return r;
}

Object.forEach = function(o, callback) {
    Object.keys(o).forEach(function(k) {
        if(o.hasOwnProperty(k)) {
            if(!callback.apply(o, [k, o[k]])) {
                return false;
            }
        }
        return true;
    });
}

Object.countKeys = function(o) {return Object.keys(o).length;};

/* String prototype expansion */
String.prototype.stripHtml = function () { return this.replace(/<[^>]+>/gim, "").replace(/<\/[^>]+>/gim, "").replace(/&nbsp;/gim, ""); }
String.prototype.ltrim = function () { return this.replace(/^\s+/,""); }
String.prototype.rtrim = function () { return this.replace(/\s+$/,""); }
String.prototype.trim = function () { return this.replace(/^\s*([\S\s]*?)\s*$/, '$1'); }
String.prototype.splitA = function (separator) {
    var retArr = new Array();
    var s = this;
    
    if (separator.length != 0) {
        var i = 0;
        while (s.indexOf(separator) != -1) { retArr[i] = s.substring(0, s.indexOf(separator)); s = s.substring(s.indexOf(separator)+separator.length, s.length+1); i++; } retArr[i] = s;
    }
    else {
        for (var i = 0; i < s.length; i++)
            retArr[i] = s.substring(i, i+1);
    }
    return retArr;
}
String.prototype.toInt = function() {
    return this/1;
}
String.prototype.isFinite = function () { return isFinite(this); }
String.prototype.isNumeric = function () { return this.isFinite((this * 1.0)); }
String.prototype.isEmail = function () {
    if (this.indexOf(" ") != -1) return false;
    else if (this.indexOf("@") == -1) return false;
    else if (this.indexOf("@") == 0) return false;
    else if (this.indexOf("@") == (this.length-1)) return false;
    var arrayString = this.splitA("@");
    if (arrayString[1].indexOf(".") == -1)
        return false;
    else if (arrayString[1].indexOf(".") == 0)
        return false;
    else if (arrayString[1].charAt(arrayString[1].length-1) == ".")
        return false;
    return true;
}
String.prototype.repeat = function(n){
    var a = [];
    var s = this;
    while(a.length < n)
        a.push(s);
    return a.join('');
}
String.prototype.expand = function(c, l) {
    if( this.length >= l )
        return this;
    else
        return c.repeat(l - this.length) + this;
}
String.prototype.toDate = function() {
    var t = this.replace('T', ' ');
    t = t.split('+')[0];
    var parts = t.split(' ');
    var dateParts = parts[0].split('-');
    var timeParts = parts[1] ? parts[1].split(':') : ['0', '0', '0'];
    return new Date((dateParts[0] + '').toInt(), (dateParts[1] + '').toInt()-1, (dateParts[2] + '').toInt(), (timeParts[0] + '').toInt(), (timeParts[1] + '').toInt(), (timeParts[2] + '').toInt());
}
String.prototype.toShortDate = function() {
    var parts = this.split('/');
    return new Date(parseInt(parts[2]), parseInt(parts[1]), parts[0]);
                           
}         
String.prototype.words = function(l) {
    var a = this.split(/ |,|\.|-|;|:|\(|\)|\{|\}|\[|\]/);
    
    if (a.length > 0) {
        if (a.length == 1)
            return this + '';
        else if(a.length < l)
            return this + '';
        
        i = 0;
        for(j=0; j<l;j++) {
            i = i + a[j].length+1;
        }
        
        return this.substr(0, i) + '...';
    }
    else {
        return this.substr(0, l) + '...';
    }
}
String.prototype.replaceAll = function(from, to) {
    var s = this;
    var s1 = s.replace(from, to);
    while(s != s1) {
        s = s1;
        s1 = s.replace(from, to);
    }
    return s1;
}
String.prototype.replaceArray = function(from, to) {
    var ret = this;
    from.forEach(function(el) {
        ret = ret.replaceAll(el, to);
    });
    return ret;
}
String.prototype.fromMoney = function() {
    return parseInt(val.replace(/\s*/g,''));
}
String.prototype.fromTimeString = function() {
    var parts = this.split(':');
    return parseInt(parseInt(parts[2]) + parseInt(parts[1]) * 60 + parseInt(parts[0]) * 60 * 60);
}
String.prototype.capitalize = function() {
    return this.substr(0, 1).toUpperCase() + this.substr(1);
}
String.prototype.Transliterate = function() {
    var val = this;
    
    A = new Array();
    A["Ё"]="YO";A["Й"]="I";A["Ц"]="TS";A["У"]="U";A["К"]="K";A["Е"]="E";A["Н"]="N";A["Г"]="G";A["Ш"]="SH";A["Щ"]="SCH";A["З"]="Z";A["Х"]="H";A["Ъ"]="'";
    A["ё"]="yo";A["й"]="i";A["ц"]="ts";A["у"]="u";A["к"]="k";A["е"]="e";A["н"]="n";A["г"]="g";A["ш"]="sh";A["щ"]="sch";A["з"]="z";A["х"]="h";A["ъ"]="'";
    A["Ф"]="F";A["Ы"]="I";A["В"]="V";A["А"]="A";A["П"]="P";A["Р"]="R";A["О"]="O";A["Л"]="L";A["Д"]="D";A["Ж"]="ZH";A["Э"]="E";
    A["ф"]="f";A["ы"]="i";A["в"]="v";A["а"]="a";A["п"]="p";A["р"]="r";A["о"]="o";A["л"]="l";A["д"]="d";A["ж"]="zh";A["э"]="e";
    A["Я"]="YA";A["Ч"]="CH";A["С"]="S";A["М"]="M";A["И"]="I";A["Т"]="T";A["Ь"]="'";A["Б"]="B";A["Ю"]="YU";
    A["я"]="ya";A["ч"]="ch";A["с"]="s";A["м"]="m";A["и"]="i";A["т"]="t";A["ь"]="'";A["б"]="b";A["ю"]="yu";                                                                                                                                                                                                             
    
    val = val.replace(/([\u0410-\u0451])/g,
        function (str,p1,offset,s) {
            if (A[str] != 'undefined'){return A[str];}
        }
    )
    return val;
};
String.prototype.CyrToUrl = function(words) {                                                            
    if(words == undefined) words = 3;
    
    var val = this.Transliterate()
        .trim()
        .replaceArray([" ","|",".",",","(",")","[","]","!","@",":",";","*","#","$","%","^"], "-")
        .replaceArray(["'","?",'"','…','&quot;',"\\","/",'«','»',/[0-9]/i], "")
        .replaceAll('--', '-')
        .toLowerCase();
        
    val = val.split('-');
    var v = [];
    val.forEach(function(vv) {
        v.push(vv.trim());
    });
    val = v.splice(0, words).join('-');
    return val.trim();
    
}
String.prototype.ellipsis = function(length) {
    var str = this;
    if(!str)
        return str;
    
    str = str + '';
        
    strlen = str.length;
    if(strlen <= length)
        return str;
    
    
    cliplen = parseInt((length - 3)/2);
    return str.substr(0, cliplen) + '...' + str.substr(strlen - cliplen - 1, strlen);
}

String.GUID = function() {
   return (Number.Rnd4() + Number.Rnd4() + Number.Rnd4() + Number.Rnd4() + Number.Rnd4() + Number.Rnd4() + Number.Rnd4() + Number.Rnd4());
};

/* number prototype expansion */
Number.prototype.toDateFromUnixTime = function() { var d = new Date(); d.setTime(this * 1000); return d; }
Number.prototype.formatSequence = function($labels, $viewnumber) {
    $s = this + " ";
    if(!$viewnumber || $viewnumber == undefined)
        $s = "";
    
    $ssecuence = this + '';
    $sIntervalLastChar = $ssecuence.substr($ssecuence.length-1, 1);
    if(parseInt($ssecuence) > 10 && parseInt($ssecuence) < 20)
        return $s + $labels[2];
    else {
        switch(parseInt($sIntervalLastChar)) {
            case 1:
                return $s + $labels[0];
            case 2:
            case 3:
            case 4:
                return $s + $labels[1];
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 0:
                return $s + $labels[2];
        }
    }
}
Number.prototype.decPlaces = function() {
    var n = this + '';
    n = n.split('.');
    if(n.length <= 1)
        return 0;
    return n[1].length;
}
Number.prototype.toMoney = function(digits, force) {
    var result = '';                
    if(digits == undefined)
        digits = 2;

    var price = this.toFixed(digits);
    var price = '' + price;
    var parts = price.split(/\.|\,/);
    
    price = parts[0];
    var dec = ( parts[1] != null ) ? parts[1] : '';

    var len = price.length;
    var count = Math.floor( len / 3 );
    
    for(var i = 0; i < count; i++) {
        result = (!( i == (count - 1) && len % 3 == 0) ? ' ' : '') + price.substr( len - (i + 1) * 3, 3) + result;
    }

    result = price.substr(0, len - count*3) + result;
    return result + ( dec ? ',' + dec : (force ? ',' + '0'.repeat(digits) : ''));
}
Number.prototype.toTimeString = function() {
    var date = new Date(null);
    date.setSeconds(this + 0);
    return date.toISOString().substr(11, 8);
}
Number.prototype.toSizeString = function(postfixes, range) {
    var number = this;
    for(j=0; j < postfixes.length; j++) {
        if(number <= range)
            break;
        else
            number = number/range;
    }
    number = number.toFixed(2);
    return number + " " + postfixes[j];
}
Number.random = function(min, max) {
    return Math.floor(min + Math.random()*(max + 1));
}
Number.Rnd4 = function() {
   return (((1+Math.random()) * 0x10000)|0).toString(16).substring(1);
};

/* date prototype expansion */
Date.prototype.toDbDate = function() {
    return this.getFullYear() + '-' + ((this.getMonth() + 1) + '').expand('0', 2) + '-' + (this.getDate() + '').expand('0', 2) + ' ' + (this.getHours() + '').expand('0', 2) + ':' + (this.getMinutes() + '').expand('0', 2) + ':' + (this.getSeconds() + '').expand('0', 2);
}
Date.prototype.toUnixTime = function() {
    return this.getTime() / 1000;
}
Date.prototype.toShortDateString = function() {
    return this.getFullYear() + '-' + ((this.getMonth() + 1) + '').expand('0', 2) + '-' + (this.getDate() + '').expand('0', 2);
}
Date.prototype.toTimeString = function() {
    return (this.getHours() + '').expand('0', 2) + ':' + (this.getMinutes() + '').expand('0', 2) + ':' + (this.getSeconds() + '').expand('0', 2);
}
Date.prototype.timezoneoffset = (new Date()).getTimezoneOffset() / 60;
Date.prototype.toLocalTime = function () { this.setTime(this.getTime() - this.timezoneoffset*60*60*1000); return this; };
Date.prototype.addMinute = function (min) { this.setTime(this.getTime() + min*60*1000); return this; } 
Date.prototype.Diff = function (dt) { return parseInt((dt.getTime() - this.getTime()) / 1000); } 
Date.prototype.Age = function() {
    $time = (new Date()).getTime()/1000 - this.getTime()/1000; // to get the time since that moment

    $tokens = [
        [31536000, ['год', 'года', 'лет']],
        [2592000, ['месяц', 'месяца', 'месяцев']],
        [604800, ['неделю', 'недели', 'недель']],
        [86400, ['день', 'дня', 'дней']],
        [3600, ['час', 'часа', 'часов']],
        [60, ['минуту', 'минуты', 'минут']],
        [1, ['секунду', 'секунды', 'секунд']]
    ];

    for($u=0; $u<$tokens.length; $u++) {
        $labels = $tokens[$u][1];
        $unit = $tokens[$u][0];

        if ($time < parseInt($unit)) continue;
        $numberOfUnits = Math.floor($time / $unit);
        $ret = ($numberOfUnits > 1 ? $numberOfUnits + ' ' : '') + $numberOfUnits.formatSequence($labels, false) + ' назад';
        if($ret == 'день назад')
            $ret = 'вчера';
        return $ret;
    }
    
    return 'только что';
}
Date.prototype.format = function(formatString) { return this.toString(formatString); }
Date.prototype.DayIndex = function() {
    var start = new Date(this.getFullYear(), 0, 0);
    var diff = this - start;
    var oneDay = 1000 * 60 * 60 * 24;
    return Math.floor(diff / oneDay);           
}
Date.Now = function() { return new Date(); }

/* object length */
var length = function(o) {
    var i = 0;
    for(k in o) {
        i++;
    }
    return i;
}

/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.2 Copyright (C) Paul Johnston 1999 - 2009
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */
var hexcase=0;function hex_md5(a){return rstr2hex(rstr_md5(str2rstr_utf8(a)))}function hex_hmac_md5(a,b){return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a),str2rstr_utf8(b)))}function md5_vm_test(){return hex_md5("abc").toLowerCase()=="900150983cd24fb0d6963f7d28e17f72"}function rstr_md5(a){return binl2rstr(binl_md5(rstr2binl(a),a.length*8))}function rstr_hmac_md5(c,f){var e=rstr2binl(c);if(e.length>16){e=binl_md5(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binl_md5(a.concat(rstr2binl(f)),512+f.length*8);return binl2rstr(binl_md5(d.concat(g),512+128))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binl(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(c%32)}return a}function binl2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(c%32))&255)}return a}function binl_md5(p,k){p[k>>5]|=128<<((k)%32);p[(((k+64)>>>9)<<4)+14]=k;var o=1732584193;var n=-271733879;var m=-1732584194;var l=271733878;for(var g=0;g<p.length;g+=16){var j=o;var h=n;var f=m;var e=l;o=md5_ff(o,n,m,l,p[g+0],7,-680876936);l=md5_ff(l,o,n,m,p[g+1],12,-389564586);m=md5_ff(m,l,o,n,p[g+2],17,606105819);n=md5_ff(n,m,l,o,p[g+3],22,-1044525330);o=md5_ff(o,n,m,l,p[g+4],7,-176418897);l=md5_ff(l,o,n,m,p[g+5],12,1200080426);m=md5_ff(m,l,o,n,p[g+6],17,-1473231341);n=md5_ff(n,m,l,o,p[g+7],22,-45705983);o=md5_ff(o,n,m,l,p[g+8],7,1770035416);l=md5_ff(l,o,n,m,p[g+9],12,-1958414417);m=md5_ff(m,l,o,n,p[g+10],17,-42063);n=md5_ff(n,m,l,o,p[g+11],22,-1990404162);o=md5_ff(o,n,m,l,p[g+12],7,1804603682);l=md5_ff(l,o,n,m,p[g+13],12,-40341101);m=md5_ff(m,l,o,n,p[g+14],17,-1502002290);n=md5_ff(n,m,l,o,p[g+15],22,1236535329);o=md5_gg(o,n,m,l,p[g+1],5,-165796510);l=md5_gg(l,o,n,m,p[g+6],9,-1069501632);m=md5_gg(m,l,o,n,p[g+11],14,643717713);n=md5_gg(n,m,l,o,p[g+0],20,-373897302);o=md5_gg(o,n,m,l,p[g+5],5,-701558691);l=md5_gg(l,o,n,m,p[g+10],9,38016083);m=md5_gg(m,l,o,n,p[g+15],14,-660478335);n=md5_gg(n,m,l,o,p[g+4],20,-405537848);o=md5_gg(o,n,m,l,p[g+9],5,568446438);l=md5_gg(l,o,n,m,p[g+14],9,-1019803690);m=md5_gg(m,l,o,n,p[g+3],14,-187363961);n=md5_gg(n,m,l,o,p[g+8],20,1163531501);o=md5_gg(o,n,m,l,p[g+13],5,-1444681467);l=md5_gg(l,o,n,m,p[g+2],9,-51403784);m=md5_gg(m,l,o,n,p[g+7],14,1735328473);n=md5_gg(n,m,l,o,p[g+12],20,-1926607734);o=md5_hh(o,n,m,l,p[g+5],4,-378558);l=md5_hh(l,o,n,m,p[g+8],11,-2022574463);m=md5_hh(m,l,o,n,p[g+11],16,1839030562);n=md5_hh(n,m,l,o,p[g+14],23,-35309556);o=md5_hh(o,n,m,l,p[g+1],4,-1530992060);l=md5_hh(l,o,n,m,p[g+4],11,1272893353);m=md5_hh(m,l,o,n,p[g+7],16,-155497632);n=md5_hh(n,m,l,o,p[g+10],23,-1094730640);o=md5_hh(o,n,m,l,p[g+13],4,681279174);l=md5_hh(l,o,n,m,p[g+0],11,-358537222);m=md5_hh(m,l,o,n,p[g+3],16,-722521979);n=md5_hh(n,m,l,o,p[g+6],23,76029189);o=md5_hh(o,n,m,l,p[g+9],4,-640364487);l=md5_hh(l,o,n,m,p[g+12],11,-421815835);m=md5_hh(m,l,o,n,p[g+15],16,530742520);n=md5_hh(n,m,l,o,p[g+2],23,-995338651);o=md5_ii(o,n,m,l,p[g+0],6,-198630844);l=md5_ii(l,o,n,m,p[g+7],10,1126891415);m=md5_ii(m,l,o,n,p[g+14],15,-1416354905);n=md5_ii(n,m,l,o,p[g+5],21,-57434055);o=md5_ii(o,n,m,l,p[g+12],6,1700485571);l=md5_ii(l,o,n,m,p[g+3],10,-1894986606);m=md5_ii(m,l,o,n,p[g+10],15,-1051523);n=md5_ii(n,m,l,o,p[g+1],21,-2054922799);o=md5_ii(o,n,m,l,p[g+8],6,1873313359);l=md5_ii(l,o,n,m,p[g+15],10,-30611744);m=md5_ii(m,l,o,n,p[g+6],15,-1560198380);n=md5_ii(n,m,l,o,p[g+13],21,1309151649);o=md5_ii(o,n,m,l,p[g+4],6,-145523070);l=md5_ii(l,o,n,m,p[g+11],10,-1120210379);m=md5_ii(m,l,o,n,p[g+2],15,718787259);n=md5_ii(n,m,l,o,p[g+9],21,-343485551);o=safe_add(o,j);n=safe_add(n,h);m=safe_add(m,f);l=safe_add(l,e)}return Array(o,n,m,l)}function md5_cmn(h,e,d,c,g,f){return safe_add(bit_rol(safe_add(safe_add(e,h),safe_add(c,f)),g),d)}function md5_ff(g,f,k,j,e,i,h){return md5_cmn((f&k)|((~f)&j),g,f,e,i,h)}function md5_gg(g,f,k,j,e,i,h){return md5_cmn((f&j)|(k&(~j)),g,f,e,i,h)}function md5_hh(g,f,k,j,e,i,h){return md5_cmn(f^k^j,g,f,e,i,h)}function md5_ii(g,f,k,j,e,i,h){return md5_cmn(k^(f|(~j)),g,f,e,i,h)}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
function hex2bin(h) { var r = ""; for (var i= (h.substr(0, 2)=="0x")?2:0; i<h.length; i+=2) {r += String.fromCharCode (parseInt (h.substr (i, 2), 16));} return r; }
function bin2hex(bin) { var result = ""; var temp = ""; for(var i=0; i < bin.length; i++) { var chr = bin.charCodeAt(i); if(chr > 127){ chr = encodeURIComponent(bin.charAt(i)); } else { chr = chr.toString(16); } result += chr; }  for(var i=0; i < result.length ;i++) { var chr = result.charAt(i); if(chr != '%') temp += chr; }  return temp.toLowerCase(); } 

/* debug printing functions */
var out = function() {
    
    if(!_DEBUG_MODE) return ;
    
    console.log.apply(console, arguments);
    return;
    
    try {
        var args = new Array();
        args[args.length] = (new Date()).toLocaleTimeString();
        args[args.length] = ':';
        for(var i=0; i<arguments.length;i++) {
            args[args.length] = arguments[i];
            args[args.length] = ',';
        }
        console.log.apply(this, args);
    }
    catch(e) {}
};

/* cookie */
var Cookie = function() {};
Cookie.set = function (c_name, value, exdays, path, domain) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value) + ((exdays==null) ? "" : "; expires=" + exdate.toUTCString()) + (path == null ? '; path=/' : '; path=' + path);
    document.cookie = c_name + "=" + c_value;
};
Cookie.get = function (c_name) {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for( i=0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g,"");
        if(x == c_name)
            return unescape(y);
    }
    return null;
};

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/       
var Base64 = {
 
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
 
        input = Base64._utf8_encode(input);
 
        while (i < input.length) {
 
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
 
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
 
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
 
            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
        }
 
        return output;
    },
 
    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
 
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
        while (i < input.length) {
 
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
 
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
 
            output = output + String.fromCharCode(chr1);
 
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
 
        }
 
        output = Base64._utf8_decode(output);
 
        return output;
 
    },
 
    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";
 
        for (var n = 0; n < string.length; n++) {
 
            var c = string.charCodeAt(n);
 
            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
 
        }
 
        return utftext;
    },
 
    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
 
        while ( i < utftext.length ) {
 
            c = utftext.charCodeAt(i);
 
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
 
        }
 
        return string;
    }
 
}

!function(e,n){"object"==typeof exports&&"undefined"!=typeof module?n():"function"==typeof define&&define.amd?define(n):n()}(0,function(){"use strict";function e(e){function n(){var d=Date.now()-l;d<i&&d>=0?r=setTimeout(n,i-d):(r=null,t||(f=e.apply(u,o),u=null,o=null))}var i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:100,t=arguments[2],r=void 0,o=void 0,u=void 0,l=void 0,f=void 0,d=function(){u=this;for(var d=arguments.length,a=Array(d),s=0;s<d;s++)a[s]=arguments[s];o=a,l=Date.now();var c=t&&!r;return r||(r=setTimeout(n,i)),c&&(f=e.apply(u,o),u=null,o=null),f};return d.clear=function(){r&&(clearTimeout(r),r=null)},d.flush=function(){r&&(f=e.apply(u,o),u=null,o=null,clearTimeout(r),r=null)},d}var n=window.jQuery;if(!n)throw new Error("resizeend require jQuery");n.event.special.resizeend={setup:function(){var i=n(this);i.on("resize.resizeend",e.call(null,function(e){e.type="resizeend",i.trigger("resizeend",e)},250))},teardown:function(){n(this).off("resize.resizeend")}}});


MimeType = {};
MimeType.types = {
        "acx" :  "application/internet-property-stream",
        "ai" :  "application/postscript",
        "aif" :  "audio/x-aiff",
        "aifc" :  "audio/x-aiff",
        "aiff" :  "audio/x-aiff",
        "asf" :  "video/x-ms-asf",
        "asr" :  "video/x-ms-asf",
        "asx" :  "video/x-ms-asf",
        "au" :  "audio/basic",
        "avi" :  "video/x-msvideo",
        "flv" :  "video/x-msvideo",
        "axs" :  "application/olescript",
        "bas" :  "text/plain",
        "bcpio" :  "application/x-bcpio",
        "bin" :  "application/octet-stream",
        "bmp" :  "image/bmp",
        "c" :  "text/plain",
        "cat" :  "application/vnd.ms-pkiseccat",
        "cdf" :  "application/x-cdf",
        "cer" :  "application/x-x509-ca-cert",
        "class" :  "application/octet-stream",
        "clp" :  "application/x-msclip",
        "cmx" :  "image/x-cmx",
        "cod" :  "image/cis-cod",
        "cpio" :  "application/x-cpio",
        "crd" :  "application/x-mscardfile",
        "crl" :  "application/pkix-crl",
        "crt" :  "application/x-x509-ca-cert",
        "csh" :  "application/x-csh",
        "css" :  "text/css",
        "dcr" :  "application/x-director",
        "der" :  "application/x-x509-ca-cert",
        "dir" :  "application/x-director",
        "dll" :  "application/x-msdownload",
        "dms" :  "application/octet-stream",
        "doc" :  "application/msword",
        "docx" :  "application/msword",
        "dot" :  "application/msword",
        "dvi" :  "application/x-dvi",
        "dxr" :  "application/x-director",
        "eps" :  "application/postscript",
        "etx" :  "text/x-setext",
        "evy" :  "application/envoy",
        "exe" :  "application/octet-stream",
        "fif" :  "application/fractals",
        "flr" :  "x-world/x-vrml",
        "gif" :  "image/gif",
        "gtar" :  "application/x-gtar",
        "gz" :  "application/x-gzip",
        "h" :  "text/plain",
        "hdf" :  "application/x-hdf",
        "hlp" :  "application/winhlp",
        "hqx" :  "application/mac-binhex40",
        "hta" :  "application/hta",
        "htc" :  "text/x-component",
        "html" :  "text/html",
        "htt" :  "text/webviewhtml",
        "ico" :  "image/x-icon",
        "ief" :  "image/ief",
        "iii" :  "application/x-iphone",
        "ins" :  "application/x-internet-signup",
        "isp" :  "application/x-internet-signup",
        "jfif" :  "image/pipeg",
        "jpe" :  "image/jpeg",
        "jpeg" :  "image/jpeg",
        "jpg" :  "image/jpeg",
        "png" :  "image/png",
        "js" :  "text/javascript",
        "latex" :  "application/x-latex",
        "lha" :  "application/octet-stream",
        "lsf" :  "video/x-la-asf",
        "lsx" :  "video/x-la-asf",
        "lzh" :  "application/octet-stream",
        "m13" :  "application/x-msmediaview",
        "m14" :  "application/x-msmediaview",
        "m3u" :  "audio/x-mpegurl",
        "man" :  "application/x-troff-man",
        "mdb" :  "application/x-msaccess",
        "me" :  "application/x-troff-me",
        "mht" :  "message/rfc822",
        "mhtml" :  "message/rfc822",
        "mid" :  "audio/mid",
        "mny" :  "application/x-msmoney",
        "mov" :  "video/quicktime",
        "movie" :  "video/x-sgi-movie",
        "mp2" :  "video/mpeg",
        "mp3" :  "audio/mpeg",
        "mpa" :  "video/mpeg",
        "mpe" :  "video/mpeg",
        "mpeg" :  "video/mpeg",
        "mpg" :  "video/mpeg",
        "mp4" :  "video/mp4",
        "mpp" :  "application/vnd.ms-project",
        "mpv2" :  "video/mpeg",
        "ms" :  "application/x-troff-ms",
        "mvb" :  "application/x-msmediaview",
        "nws" :  "message/rfc822",
        "oda" :  "application/oda",
        "p10" :  "application/pkcs10",
        "p12" :  "application/x-pkcs12",
        "p7b" :  "application/x-pkcs7-certificates",
        "p7c" :  "application/x-pkcs7-mime",
        "p7m" :  "application/x-pkcs7-mime",
        "p7r" :  "application/x-pkcs7-certreqresp",
        "p7s" :  "application/x-pkcs7-signature",
        "pbm" :  "image/x-portable-bitmap",
        "pdf" :  "application/pdf",
        "pfx" :  "application/x-pkcs12",
        "pgm" :  "image/x-portable-graymap",
        "pko" :  "application/ynd.ms-pkipko",
        "pma" :  "application/x-perfmon",
        "pmc" :  "application/x-perfmon",
        "pml" :  "application/x-perfmon",
        "pmr" :  "application/x-perfmon",
        "pmw" :  "application/x-perfmon",
        "pnm" :  "image/x-portable-anymap",
        "pot":  "application/vnd.ms-powerpoint",
        "ppm" :  "image/x-portable-pixmap",
        "pps" :  "application/vnd.ms-powerpoint",
        "ppt" :  "application/vnd.ms-powerpoint",
        "prf" :  "application/pics-rules",
        "ps" :  "application/postscript",
        "pub" :  "application/x-mspublisher",
        "qt" :  "video/quicktime",
        "ra" :  "audio/x-pn-realaudio",
        "ram" :  "audio/x-pn-realaudio",
        "ras" :  "image/x-cmu-raster",
        "rgb" :  "image/x-rgb",
        "rmi" :  "audio/mid",
        "roff" :  "application/x-troff",
        "rtf" :  "application/rtf",
        "rtx" :  "text/richtext",
        "scd" :  "application/x-msschedule",
        "sct" :  "text/scriptlet",
        "setpay" :  "application/set-payment-initiation",
        "setreg" :  "application/set-registration-initiation",
        "sh" :  "application/x-sh",
        "shar" :  "application/x-shar",
        "sit" :  "application/x-stuffit",
        "snd" :  "audio/basic",
        "spc" :  "application/x-pkcs7-certificates",
        "spl" :  "application/futuresplash",
        "src" :  "application/x-wais-source",
        "sst" :  "application/vnd.ms-pkicertstore",
        "stl" :  "application/vnd.ms-pkistl",
        "sv4cpio" :  "application/x-sv4cpio",
        "sv4crc" :  "application/x-sv4crc",
        "t" :  "application/x-troff",
        "tar" :  "application/x-tar",
        "tcl" :  "application/x-tcl",
        "tex" :  "application/x-tex",
        "texi" :  "application/x-texinfo",
        "texinfo" :  "application/x-texinfo",
        "tgz" :  "application/x-compressed",
        "tif" :  "image/tiff",
        "tiff" :  "image/tiff",
        "tr" :  "application/x-troff",
        "trm" :  "application/x-msterminal",
        "tsv" :  "text/tab-separated-values",
        "txt" :  "text/plain",
        "uls" :  "text/iuls",
        "ustar" :  "application/x-ustar",
        "vcf" :  "text/x-vcard",
        "vrml" :  "x-world/x-vrml",
        "wav" :  "audio/x-wav",
        "wcm" :  "application/vnd.ms-works",
        "wdb" :  "application/vnd.ms-works",
        "wks" :  "application/vnd.ms-works",
        "wmf" :  "application/x-msmetafile",
        "wps" :  "application/vnd.ms-works",
        "wri" :  "application/x-mswrite",
        "wrl" :  "x-world/x-vrml",
        "wrz" :  "x-world/x-vrml",
        "xaf" :  "x-world/x-vrml",
        "xbm" :  "image/x-xbitmap",
        "xla" :  "application/vnd.ms-excel",
        "xlc" :  "application/vnd.ms-excel",
        "xlm" :  "application/vnd.ms-excel",
        "xls" :  "application/vnd.ms-excel",
        "xlsx" :  "application/vnd.ms-excel",
        "xlt" :  "application/vnd.ms-excel",
        "xlw" :  "application/vnd.ms-excel",
        "xof" :  "x-world/x-vrml",
        "xpm" :  "image/x-xpixmap",
        "xwd" :  "image/x-xwindowdump",
        "z" :  "application/x-compress",
        "zip" :  "application/zip", 
        "swf" : "application/x-shockwave-flash"
        
    };
MimeType.ext2type = function(ext) {
    return MimeType.types[ext];
};
MimeType.type2ext = function(type) {
    var ret = false;
    Object.keys(MimeType.types).forEach(key => {
        if(type == MimeType.types[key]) { ret = key; return false; }
        return true;
    });
    return ret;
};
MimeType.isImage = function(ext) { return ["gif", "jpeg", "jpg", "png", "bmp", "dib", "svg"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isAudio = function(ext) { return ["mid", "mp3", "au"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isVideo = function(ext) { return ["wmv", "mpg", "mp4"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isFlash = function(ext) { return ["swf"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isEditable = function(ext) { return ["txt", "js", "css", "layout", "php", "htm", "html", "service"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isBrowserCapable = function(ext) { return ["jpg", "png", "gif", "swf", "html", "htm", "txt", "css", "js", "xml", "xsl", "pdf", "wmv", "mpg", "mp4", "mid", "mp3", "au"].indexOf(ext.toLowerCase()) != -1; };
MimeType.isViewable = function(ext) { return MimeType.isImage(ext) || MimeType.isFlash(ext); };
MimeType.icon = function(ext) { return 'icon-file-' + ext; };
MimeType.ext2mode = function(ext) {
    
    if(ext == 'js') return 'javascript';
    else if(ext == 'css') return 'css';
    else if(ext == 'less') return 'less';
    else if(ext == 'html' || ext == 'htm') return 'htmlmixed';
    else if(ext == 'php' || ext == 'layout' || ext == 'release' || ext == 'service') return 'php';
    else if(ext == 'xml') return 'xml';
    else if(ext == 'yaml') return 'yaml';
    else return MimeType.ext2type(ext);
    
}

MimeType.extrequirements = function(ext) {
    
    if(ext == 'js') return {js: ['res/codemirror/mode/javascript/javascript.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else if(ext == 'css' || ext == 'less') return {js: ['res/codemirror/mode/css/css.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else if(ext == 'html' || ext == 'htm') return {js: ['res/codemirror/mode/htmlmixed/htmlmixed.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else if(ext == 'php' || ext == 'layout' || ext == 'release' || ext == 'service') return {js: ['res/codemirror/mode/htmlmixed/htmlmixed.js', 'res/codemirror/mode/xml/xml.js', 'res/codemirror/mode/javascript/javascript.js', 'res/codemirror/mode/css/css.js', 'res/codemirror/mode/clike/clike.js', 'res/codemirror/mode/php/php.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else if(ext == 'xml') return {js: ['res/codemirror/mode/xml/xml.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else if(ext == 'yaml') return {js: ['res/codemirror/mode/yaml/yaml.js', 'res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    else return {js: ['res/codemirror/addon/selection/active-line.js', 'res/codemirror/addon/edit/matchbrackets.js', 'res/codemirror/addon/fold/foldcode.js', 'res/codemirror/addon/fold/foldgutter.js', 'res/codemirror/addon/fold/brace-fold.js', 'res/codemirror/addon/fold/xml-fold.js', 'res/codemirror/addon/fold/indent-fold.js', 'res/codemirror/addon/fold/comment-fold.js', 'res/codemirror/addon/dialog/dialog.js', 'res/codemirror/addon/search/searchcursor.js', 'res/codemirror/addon/search/search.js', 'res/codemirror/addon/scroll/annotatescrollbar.js', 'res/codemirror/addon/search/matchesonscrollbar.js', 'res/codemirror/addon/search/jump-to-line.js'], css: ['res/codemirror/addon/fold/foldgutter.css', 'res/codemirror/addon/dialog/dialog.css', 'res/codemirror/addon/search/matchesonscrollbar.css']};
    
}

/* кое какая хуйня */
function findObject(ar, v, field, ret) {
    if(ar == undefined) return false;
    if(field == undefined) field = 'value';
    if(ret == undefined) ret = 'title';
    
    for(var i=0; i<ar.length; i++) {
        // console.log(i, field, ar[i][field], v);
        if(ar[i][field] == v) {
            return ret == undefined ? ar[i] : ar[i][ret];
        }
    }
    
    return false;
    
}

function file(v) {      
    if(!v)
        return "";
    var exts = v.split('.');
    var ext = exts[exts.length - 1];
    if(MimeType.isImage(ext))
        return "<img src=\'" + _ROOTPATH + "res/img/1x1.gif\' style=\'background: url(" + v + ") center center no-repeat; background-size: contain;\' height=\'80\' width=\'80\' />";
    else
        return "<span class='ui-image' rel='" + MimeType.icon(ext) + "' title='" + v + "'></span>";
        
}

function files(v) {
    if(!v) return "";
    var ret = [];
    v.split(';').forEach(function(vv) {
        ret.push(file(vv));
    });
    return ret.join("");        
}

function pathinfo(path) {
    try {
        var parts = path.split('/');
        var ret = {};
        ret.basename = parts[parts.length - 1];
        
        var fileparts = ret.basename.split('.');
        ret.ext = fileparts.length > 1 ? fileparts[fileparts.length - 1] : '';
        ret.filename = fileparts[0];
        
        ret.dirname = path.replaceAll(ret.basename, '');
        
        return ret;
    }
    catch (e) {
        return {};
    }
}

function paramsToObject(a) {
    var obj = {};
    a.forEach(function(o) {
        obj[o.name] = o.value;
    })
    return obj;
}

function _wait(ec,f){
    try{
        eval('var t='+ec+';');
        if(t) {
            f();
        }
        else {
            setTimeout(function(){
                _wait(ec,f);
            }, 100);
        }
    }
    catch(e) {
        console.log(e); 
        setTimeout(function(){
            _wait(ec, f);
        }, 100);
    }
};
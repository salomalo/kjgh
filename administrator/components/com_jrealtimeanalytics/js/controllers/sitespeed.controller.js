(function(c){var d=function(i){var l={container:"sitespeedtest_container_element",databind:"application_panel",targetURL:"http://www.google.it",useNoCache:true};var j;var k;var b=10;this.getModel=function(){var e=new JRealtimeModelSitespeed(l);return e};this.getView=function(){var e=new JRealtimeViewSitespeed(l);return e};this.executeTest=function(e){if(e){b.empty();b.add(e)}k.showWaiter(l.container);j.getData(b)};(function a(){c.extend(l,i);b=c.Callbacks();j=this.getModel();k=this.getView();var e=function(f){k.renderStats(f)};this.executeTest(e)}).call(this)};window.JRealtimeControllerSitespeed=d})(jQuery);
!function(e){e.JSortableList=function(t,r,o,a,s,i){var n=this;"desc"!=o&&(o="asc");var d=e.extend({orderingIcon:"add-on",orderingWrapper:"input-prepend",orderingGroup:"sortable-group-id",sortableClassName:"dndlist-sortable",placeHolderClassName:"dnd-list-highlight dndlist-place-holder",sortableHandle:".sortable-handler"},s);e("tr",t).removeClass(d.sortableClassName).addClass(d.sortableClassName),e(t).parents("table").css("position","relative"),e(d.sortableHandle,t).css("cursor","move"),e("#"+r).attr("autocomplete","off");var l=e(d.sortableHandle,e(t)).length>0?d.sortableHandle:"";e(t).sortable({axis:"y",cursor:"move",handle:l,items:"tr."+d.sortableClassName,placeholder:d.placeHolderClassName,helper:function(t,r){return e(r).css({left:"0px"}),r.children().each(function(){e(this).width(e(this).width())}),e(r).children("td").addClass("dndlist-dragged-row"),r},start:function(r,o){n.sortableGroupId=o.item.attr(d.orderingGroup),n.sortableRange=e(n.sortableGroupId?"tr["+d.orderingGroup+"="+n.sortableGroupId+"]":"."+d.sortableClassName),n.disableOtherGroupSort(r,o),i&&(n.hideChidlrenNodes(o.item.attr("item-id")),n.hideSameLevelChildrenNodes(o.item.attr("level")),e(t).sortable("refresh"))},stop:function(o,s){if(e("td",e(this)).removeClass("dndlist-dragged-row"),e(s.item).css({opacity:0}),e(s.item).animate({opacity:1},800,function(){e(s.item).css("opacity","")}),n.enableOtherGroupSort(o,s),n.rearrangeOrderingValues(n.sortableGroupId,s),a){n.cloneMarkedCheckboxes();var d=e("#"+r),l=e('input[name|="task"]',d);l.length&&l.detach(),e.post(a,d.serialize()),l.length&&l.appendTo(d),n.removeClonedCheckboxes()}n.disabledOrderingElements="",i&&(n.showChildrenNodes(s.item),n.showSameLevelChildrenNodes(s.item),e(t).sortable("refresh"))}}),this.hideChidlrenNodes=function(e){n.childrenNodes=n.getChildrenNodes(e),n.childrenNodes.hide()},this.showChildrenNodes=function(e){e.after(n.childrenNodes),n.childrenNodes.show(),n.childrenNodes=""},this.hideSameLevelChildrenNodes=function(t){n.sameLevelNodes=n.getSameLevelNodes(t),n.sameLevelNodes.each(function(){_childrenNodes=n.getChildrenNodes(e(this).attr("item-id")),_childrenNodes.addClass("child-nodes-tmp-hide"),_childrenNodes.hide()})},this.showSameLevelChildrenNodes=function(t){prevItem=t.prev(),prevItemChildrenNodes=n.getChildrenNodes(prevItem.attr("item-id")),prevItem.after(prevItemChildrenNodes),e("tr.child-nodes-tmp-hide").show().removeClass("child-nodes-tmp-hide"),n.sameLevelNodes=""},this.disableOtherGroupSort=function(){if(n.sortableGroupId){var r=e("tr["+d.orderingGroup+"!="+n.sortableGroupId+"]",e(t));r.removeClass(d.sortableClassName).addClass("dndlist-group-disabled"),e(t).sortable("refresh")}},this.enableOtherGroupSort=function(){var r=e("tr",e(t)).removeClass(d.sortableClassName);r.addClass(d.sortableClassName).removeClass("dndlist-group-disabled"),e(t).sortable("refresh")},this.disableOrderingControl=function(){e("."+d.orderingWrapper+" .add-on a",n.sortableRange).hide()},this.enableOrderingControl=function(){e("."+d.orderingWrapper+" .add-on a",n.disabledOrderingElements).show()},this.rearrangeOrderingControl=function(t){var r;n.sortableRange=e(t?"tr["+d.orderingGroup+"="+t+"]":"."+d.sortableClassName),r=n.sortableRange;var o=r.length;o>1&&(r.each(function(){var t=e("."+d.orderingWrapper+" .add-on:first a",e(this)),r=e("."+d.orderingWrapper+" .add-on:last a",e(this));t.get(0)&&r.get(0)||(t.get(0)?(t.removeAttr("title"),t=e("."+d.orderingWrapper+" .add-on:first",e(this)).html(),r=t.replace("icon-uparrow","icon-downarrow"),r=r.replace(".orderup",".orderdown"),e("."+d.orderingWrapper+" .add-on:last",e(this)).html(r)):r.get(0)&&(r.removeAttr("title"),r=e("."+d.orderingWrapper+" .add-on:last",e(this)).html(),t=r.replace("icon-downarrow","icon-uparrow"),t=t.replace(".orderdown",".orderup"),e("."+d.orderingWrapper+" .add-on:first",e(this)).html(t)))}),e("."+d.orderingWrapper+" .add-on:first a",r[0]).remove(),e("."+d.orderingWrapper+" .add-on:last a",r[o-1]).remove())},this.rearrangeOrderingValues=function(t,r){var a;n.sortableRange=e(t?"tr["+d.orderingGroup+"="+t+"]":"."+d.sortableClassName),a=n.sortableRange;var s=a.length;s>1&&(r.originalPosition.top>r.position.top?(r.item.position().top!=r.originalPosition.top&&e("[type=text]",r.item).attr("value",parseInt(e("[type=text]",r.item.next()).attr("value"))),e(a).each(function(){var t=e(this).position().top;if(r.item.get(0)!==e(this).get(0)&&t>r.item.position().top&&t<=r.originalPosition.top){if("asc"==o)var a=parseInt(e("[type=text]",e(this)).attr("value"))+1;else var a=parseInt(e("[type=text]",e(this)).attr("value"))-1;e("[type=text]",e(this)).attr("value",a)}})):r.originalPosition.top<r.position.top&&(r.item.position().top!=r.originalPosition.top&&e("[type=text]",r.item).attr("value",parseInt(e("[type=text]",r.item.prev()).attr("value"))),e(a).each(function(){var t=e(this).position().top;if(r.item.get(0)!==e(this).get(0)&&t<r.item.position().top&&t>=r.originalPosition.top){if("asc"==o)var a=parseInt(e("[type=text]",e(this)).attr("value"))-1;else var a=parseInt(e("[type=text]",e(this)).attr("value"))+1;e("[type=text]",e(this)).attr("value",a)}})))},this.cloneMarkedCheckboxes=function(){e('[name="order[]"]',e(t)).attr("name","order-tmp"),e("[type=checkbox]",n.sortableRange).each(function(){var t=e(this).clone();e(t).attr({checked:"checked",shadow:"shadow",id:""}),e("#"+r).append(e(t)),e('[name="order-tmp"]',e(this).parents("tr")).attr("name","order[]")})},this.removeClonedCheckboxes=function(){e("[shadow=shadow]").remove(),e('[name="order-tmp"]',e(t)).attr("name","order[]")},this.getChildrenNodes=function(t){return e('tr[parents~="'+t+'"]')},this.getSameLevelNodes=function(t){return e("tr[level="+t+"]")}}}(jQuery);
!function(e,t){"use strict";var i={init:function(r){if(!r)throw new Error("Invalid Element");return jQuery(function(t){var e=r,n=t(r),i=n.find("div.nr_treeselect-controls"),l=n.find("ul.nr_treeselect-ul"),c=n.find("div.nr_treeselect-menu-block").html(),s=l.css("max-height");l.find("li").each(function(){var e=t(this),n=e.find("div.nr_treeselect-item:first");e.prepend('<span class="pull-left icon-"></span>'),n.after('<div class="clearfix"></div>'),e.find("ul.nr_treeselect-sub").length&&(e.find("span.icon-").addClass("nr_treeselect-toggle icon-minus"),n.find("label:first").after(c),e.find("ul.nr_treeselect-sub ul.nr_treeselect-sub").length||e.find("div.nr_treeselect-menu-expand").remove())}),l.find("span.nr_treeselect-toggle").on("click",function(){var e=t(this);e.parent().find("ul.nr_treeselect-sub").is(":visible")?(e.removeClass("icon-minus").addClass("icon-plus"),e.parent().find("ul.nr_treeselect-sub").hide(),e.parent().find("ul.nr_treeselect-sub span.nr_treeselect-toggle").removeClass("icon-minus").addClass("icon-plus")):(e.removeClass("icon-plus").addClass("icon-minus"),e.parent().find("ul.nr_treeselect-sub").show(),e.parent().find("ul.nr_treeselect-sub span.nr_treeselect-toggle").removeClass("icon-plus").addClass("icon-minus"))}),i.find("input.nr_treeselect-filter").on("keyup",function(){var n=t(this).val().toLowerCase();l.find("li").each(function(){var e=t(this);-1==e.text().toLowerCase().indexOf(n)?e.hide():e.show()})}),i.find("a.nr_treeselect-checkall").on("click",function(){l.find("input").prop("checked",!0)}),i.find("a.nr_treeselect-uncheckall").on("click",function(){l.find("input").prop("checked",!1)}),i.find("a.nr_treeselect-toggleall").on("click",function(){l.find("input").each(function(){var e=t(this);e.prop("checked")?e.prop("checked",!1):e.prop("checked",!0)})}),i.find("a.nr_treeselect-expandall").on("click",function(){l.find("ul.nr_treeselect-sub").show(),l.find("span.nr_treeselect-toggle").removeClass("icon-plus").addClass("icon-minus")}),i.find("a.nr_treeselect-collapseall").on("click",function(){l.find("ul.nr_treeselect-sub").hide(),l.find("span.nr_treeselect-toggle").removeClass("icon-minus").addClass("icon-plus")}),i.find("a.nr_treeselect-showall").on("click",function(){l.find("li").show()}),i.find("a.nr_treeselect-showselected").on("click",function(){l.find("li").each(function(){var e=t(this),n=!0;e.find("input").each(function(){if(t(this).prop("checked"))return n=!1}),n?e.hide():e.show()})}),i.find("a.nr_treeselect-maximize").on("click",function(){l.css("max-height",""),i.find("a.nr_treeselect-maximize").hide(),i.find("a.nr_treeselect-minimize").show()}),i.find("a.nr_treeselect-minimize").on("click",function(){l.css("max-height",s),i.find("a.nr_treeselect-minimize").hide(),i.find("a.nr_treeselect-maximize").show()}),e.querySelectorAll(".checkall, .uncheckall").forEach(function(e){e.addEventListener("click",function(){var n=!!this.classList.contains("checkall");this.closest(".nr_treeselect-item").parentNode.querySelectorAll(":scope .nr_treeselect-sub input").forEach(function(e){e.checked=n})})}),e.querySelectorAll(".expandall, .collapseall").forEach(function(e){e.addEventListener("click",function(){var n=!!this.classList.contains("expandall"),e=this.closest(".nr_treeselect-item").parentNode;e.querySelectorAll("ul.nr_treeselect-sub").forEach(function(e){e.style.display=n?"block":"none"});var t=e.querySelector("ul.nr_treeselect-sub span.nr_treeselect-toggle");t.classList.remove(n?"icon-plus":"icon-minus"),t.classList.add(n?"icon-minus":"icon-plus")})})}),!0}};t.addEventListener("DOMContentLoaded",function(){var e,n;for(e=t.querySelectorAll(".nr_treeselect"),n=0;n<e.length;n++)i.init(e[n])}),e.NRTreeselect=i}(window,document);
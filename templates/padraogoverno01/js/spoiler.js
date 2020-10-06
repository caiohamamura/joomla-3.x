/**
 * @package Content Plugin ContentSpoiler for Joomla! 3.x
 * @version $Id: version 1.4
 * @file: contentspoiler.js
 * @author Dmitry Borets
 * @copyright (C) 2016-2017 - Dmitry Borets
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

function sh(id) {
    var item = document.getElementById(id);
    var style = window.getComputedStyle(item);
    var label_show = document.getElementById(id + "_show_label");
    var label_hide = document.getElementById(id + "_hide_label");
    var label_hide_b = document.getElementById(id + "_hide_label_b");
    if (style.maxHeight == "0px") {
        item.style.maxHeight = item.scrollHeight + "px";
        if (label_show != null) {
            label_show.style.display = "none";
            label_show.style.visibility = "hidden";
        }
        if (label_hide != null) {
            label_hide.style.display = "inline";
            label_hide.style.visibility = "visible";
        }
        if (label_hide_b != null) {
            label_hide_b.style.display = "inline";
            label_hide_b.style.visibility = "visible";
        }
    } else {
        item.style.maxHeight = "0px";
        if (label_show != null) {
            label_show.style.display = "inline";
            label_show.style.visibility = "visible";
        }
        if (label_hide != null) {
            label_hide.style.display = "none";
            label_hide.style.visibility = "hidden";
        }
        if (label_hide_b != null) {
            label_hide_b.style.display = "none";
            label_hide_b.style.visibility = "hidden";
        }
    }
}

function updateCarrossel() {
    jQuery('.banner-carousel').carousel({ 'interval': 10000 });
    pos = jQuery('.banner-carousel .banneritem img').eq(0).height() - jQuery('.banner-carousel .banneritem .faixa').eq(0).height();
    jQuery('.banner-carousel .carousel-indicators').css('top', pos + 'px');
    jQuery('.banner-carousel .carousel-indicators li').mouseover(function() {
        jQuery(this).click();
    });
    jQuery('.banner-carousel').on('slide.bs.carousel', function() {
        action = window.setTimeout(function() {
            jQuery('.banner-carousel .banneritem.item.next.left').mouseover();
            jQuery('.banner-carousel .banneritem.item.prev.right').mouseover();
            jQuery('.banner-carousel').carousel('cycle');
        }, 1);
    });
    jQuery(window).load(function() {
        pos = jQuery('.banner-carousel .banneritem img').eq(0).height() - jQuery('.banner-carousel .banneritem .faixa').eq(0).height();
        jQuery('.banner-carousel .carousel-indicators').css('top', pos + 'px');
    });
    carousel_addons();
}


function updateFB() {
    if (window["FB"] == undefined) return;
    var $ = jQuery;
    var fbBtn = $("#fb-share-btn,.facebook-content");
    if (fbBtn.length == 0) return;
    fbBtn.attr("data-href", window.location.href);
    FB.init({
        appId: '151183488974954',
        xfbml: true,
        version: 'v2.8'
    });
}

function updateTwitter() {
    var $ = jQuery;
    var t = $("<a href='https://twitter.com/share' class='twitter-share-button'>");
    var tjs = $('<script>').attr('src', '//platform.twitter.com/widgets.js');
    var twitEl = $(".twitter");
    if (twitEl.length == 0) return;
    twitEl.empty();
    var headingElement = $(".documentFirstHeading").children()[0];
    var theTitle = document.title;
    $(t).attr('data-text', theTitle);
    $(t).attr('data-url', location.href);
    $(t).appendTo(twitEl);
    $(tjs).appendTo(twitEl);
}

function getOuterHTML(el) {
    $ = jQuery;
    return $("<div>").append(el.clone()).html();
}

function gaUpdate(url) {
    if (window["ga"] != undefined) {
        tracker = ga.getAll()[0];
        if (tracker) {
            tracker.send("pageview", url.replace("http://smp.ifsp.edu.br", ""));
        }
    }
}

function loadContent(toLoad, toLoadSelector) {
    var $ = jQuery;
    $("#content").addClass("fade");
    $.ajax({
        url: toLoad,
        indexValue: { toLoad: toLoad },
        success: function(data, status, jqXHR) {
            var title = (new DOMParser().parseFromString(data, "text/html")).title;
            data = jQuery(data).find(toLoadSelector);
            jQuery('#content').replaceWith(data).fadeIn('fast');
            $("#content").removeClass("fade");
            $("body > div.layout > header > div.sobre > div > nav")[0].scrollIntoView();
            document.title = title;
            history.pushState(Object.assign({}, history.state, { content: getOuterHTML(jQuery("#content")) }), document.title, toLoad);
            gaUpdate(location.href);
            updateFrame();
        }
    });
}

document.onclick = function(e) {
    e = e || window.event;
    var element = e.target || e.srcElement;

    var checkLoad = function() {
        if (element.tagName == 'A') {
            if (element.href.indexOf("#") >= 0 || element.href.match(/\.[A-z0-9]+$/) != null) {
                return true;
            }
            if (element.href.startsWith("http://smp.ifsp.edu.br") && (element.href.match(/\/[^/\.]+$/) || element.href.match(/id=\d+$/))) {
                loadContent(element.href, "#content");
                if (jQuery("#navigation-section").attr("style") != "display: none;" && jQuery("#navigation-section").attr("style") != undefined) {
                    jQuery(".mainmenu-toggle").click();
                }
                return false; // prevent default action and stop event propagation
            }
        }
        return true;
    }

    if (!checkLoad()) return false;
    element = element.parentElement;
    if (!checkLoad()) return false;
};

window.addEventListener('popstate', function(event) {
    if (window.location.href.indexOf("#") >= 0) {
        return true;
    }
    $ = jQuery;
    $("#content").addClass("fade");
    $("#content").replaceWith($(history.state["content"]));
    $("#content").removeClass("fade");
    updateFrame();
});

function updateFrame() {
    updateCarrossel();
    updateFB();
    updateTwitter();
    updateWhatsApp();
    updateSpoiler();
}

function updateSpoiler() {

}

history.pushState(Object.assign({}, history.state, { content: getOuterHTML(jQuery("#content")) }), document.title);

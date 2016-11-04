            !function(n) {
    n(function() {
        function i() {
            function i() {}
            function t() {
                a(), s()
            }
            n(function() {
                var a = n(window).width(), e = 721;
                e >= a ? i() : t();
                var s=!1;
                n(window).resize(function() {
                    s!==!1 && clearTimeout(s), s = setTimeout(function() {
                        var a = n(window).width(), e = 721;
                        e >= a ? i() : t()
                    }, 200)
                })
            })
        }
        function t() {
            r.addClass("active"), w.fadeIn(300, "swing"), m.css({
                height: "100%",
                overflow: "hidden"
            })
        }
        function a() {
            r.removeClass("active"), w.fadeOut(100, "swing")
        }
        function e() {
            m.css({
                height: "auto",
                overflow: "auto"
            })
        }
        function s() {
            m.css({
                height: "auto",
                overflow: "visible"
            })
        }
        function o() {
            w.html(u.html()), r.is(":visible") && r.click(function() {
                r.hasClass("active") ? (a(), e(), n(window).scrollTop(l)) : (l = n(window).scrollTop(), t())
            }), w.find("a").click(function() {
                a(), e()
            })
        }
        function d() {
            n(".js-anim").waypoint(function(i) {
                $thisAnim = n(this.element), $thisAnim.find("[class*='js-anim_']").length && ($thisAnim.find(".js-anim_pulse").not(".animated").css("visibility", "visible").addClass("pulse animated"), $thisAnim.find(".js-anim_bounceInUp").not(".animated").css("visibility", "visible").addClass("bounceInUp animated"), $thisAnim.find(".js-anim_fadeIn").not(".animated").css("visibility", "visible").addClass("fadeIn animated"), $thisAnim.find(".js-anim_fadeInLeft").not(".animated").css("visibility", "visible").addClass("fadeInLeft animated"), $thisAnim.find(".js-anim_fadeInRight").not(".animated").css("visibility", "visible").addClass("fadeInRight animated"), $thisAnim.find(".js-anim_fadeInUp").not(".animated").css("visibility", "visible").addClass("fadeInUp animated"), $thisAnim.find(".js-anim_tada").not(".animated").css("visibility", "visible").addClass("tada animated"))
            }, {
                offset: "80%"
            })
        }
        function f() {
            n(".js-anim_out").waypoint(function(i) {
                $thisAnim = n(this.element), $thisAnim.find("[class*='js-anim_']").length && $thisAnim.find(".js-anim_fadeOut").not(".animated").addClass("fadeOut animated")
            }, {
                offset: "10%"
            })
        }
        var c = navigator.userAgent;
        c.indexOf("iPhone") > 0 || c.indexOf("iPod") > 0 || c.indexOf("Android") > 0 && c.indexOf("Mobile") > 0 ? n("head").prepend('<meta name="viewport" content="width=device-width" />') : n("head").prepend('<meta name="viewport" content="width=1060" />'), i(), n(window).load(function() {
            var i = document.getElementById("vanek").contentDocument, t = n(i), a = t.find("#animPath");
            $otherPath = t.find("path");
            var e = .5, s = 100;
            setInterval(function() {
                0 >= s && (s = 100), s -= e, a.attr("startOffset", s + "%")
            }, 120)
        }), n(".js-accordion .js-accordion_switch").on("click", function() {
            n.when(n(this).hide().next(".js-accordion_content").slideDown(800, "swing")).done(function() {
                d()
            })
        });
        var l, m = n("html,body"), h = n(".☝"), r = h.find(".sp_nav_trigger"), u = n(".nav_content"), w = n(".sp_nav_content");
        if (o(), n("a[href^=#]").click(function() {
            var i, t, a;
            return i = n(this).attr("href"), t = n(i).offset().top, a = 800, n("html, body").animate({
                scrollTop: t
            }, a), !1
        }), n(window).load(function() {
            n(".♘").fadeOut(800, "swing", function() {
                n(".♨").fadeIn(3e3, "swing")
            })
        }), n(window).on("load resize", function() {
            d(), f()
        }), - 1 != navigator.userAgent.toLowerCase().indexOf("msie"))
            window.console.log("なるほど");
        else {
            var v = ["\n                                 ▄▄▄▄▄█████████████▄▄▄▄▄                            \n                            ▄▄█████████████████████████████▄▄                       \n                        ▄▄█████████████████████████████████████▄                    \n                      ▄██████████████████████████████████████████▄▄                 \n                    ▄███████████████████████████████████████████████▄               \n                  ▄███████████████████████████████████████████████████              \n                ▄██████████████████████████████████████████████████████▄            \n               ▄███████████████▀██████████▀▀▄███████▀▀    ▀█████████████▄           \n              ▄█████████████▀ ▄▄█████▀▀▀   ▀▀▀▀▀▀            ████████████▄          \n             ▐████████████▀  ▀▀▀▀▀                            ▀███████████          \n             ███████████▀                                       ▀██████████         \n            ▄██████████▀                                         ▀█████████▄        \n            ██████████       ▄▄▄▄▄▄                   ▄▄▄▄▄▄      ▐█████████        \n           ▐█████████     ▄██▀▀▀▀▀▀▀                ▀▀▀▀▀▀▀▀██▄    ▐████████        \n           ▐████████                                                ▀███████        \n           ▐███████                                                  ███████        \n           ▐██████▀           ▄▄▄▄                     ▄▄▄▄          ▀██████        \n            ██████         ▄███████▄                 ▄███████▄       ▐██████        \n       ▄▄▄▄▄██████         ▀▐██████                   ██████ ▀         █████████▄▄   \n     ▄████████████           ██████                   ██████          ██████▀▀▀███▄ \n    ▐███     ▀███            ▀▀██▀                     ▀██▀▀          ████▀     ▀██▄\n    ███       ███                                                     ▐██▀       ███\n    ███   ▄    ██                        ▄▄██▄▄                       ▐█▀   ▐█   ███\n    ███  ▐█▄                           ▄█▀    ▀█▄                           ██   ███\n    ▀██   ██                                                                █▀   ██▀\n     ███   █▄                                                              ▀▀   ███ \n     ▐██▄                                                                      ▄██  \n       ███▄                             ▄▄█████▄                     ▄▄▄     ▄███   \n        ▀███▄▄▄▄██▄                    ▄█▀    ▀█▄                    ██████████▀    \n           ▀▀▀▀▀▀██▄                  ▐█▀      ██                   ▄██▀▀▀▀▀        \n                 ███▄                  ██▄    ▄██                  ▄██▀             \n                  ▀██▄                  ▀▀▀▀▀▀▀▀                  ▄███              \n                   ▀███▄                                         ███▀               \n                     ▀███▄                                     ▄███▀                \n                       ▀███▄▄                               ▄████▀                  \n                         ▀▀████▄▄                       ▄▄████▀▀                    \n                             ▀██████▄▄▄▄▄        ▄▄▄▄██████▀▀                       \n                                 ▀▀▀█████████████████▀▀▀                            \n"];
            window.console.log.apply(console, v)
        }
    })
}(jQuery);
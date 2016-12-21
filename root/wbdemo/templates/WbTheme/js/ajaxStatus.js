/**
 * AJAX
 *        Plugin to delete Records from a given table without a new page load (no reload)
 */
// Building a jQuery Plugin
// using the Tutorial: http://www.learningjquery.com/2007/10/a-plugin-development-pattern
// plugin definition
/*
ajaxActiveStatus OPTIONS
=====================================================================================
MODULE = 'modulename',   // (string)
DB_RECORD_TABLE: 'modulename_table',        // (string)
DB_COLUMN: 'item_id',                       // (string) the key column you will use as reference
sFTAN: ''                                   // (string) FTAN
*/

(function($) {
        $.fn.ajaxActiveStatus = function(options) {
                var aOpts = $.extend({}, $.fn.ajaxActiveStatus.defaults, options);
                $(this).find('a').removeAttr("href").css('cursor', 'pointer');
                $(this).click(function() {
                        var oLink = $(this).find('a');
                        var oElement = $(this).find('img');
                        var iRecordID = oElement.attr("id").substring(7);
                        var oRecord = $("td#" + aOpts.DB_COLUMN +'_'+ iRecordID);
                        switch(oElement.attr("src")){
                            case ThemeUrl + 'img' +"/status_1_1.png": var action = "status_1_0"; break;
                            case ThemeUrl + 'img' +"/status_1_0.png": var action = "status_1_1"; break;
                        }
                                // pregenerate the data string
                                var sDataString = 'purpose=active_status&action=active_status&DB_RECORD_TABLE='
                                                +aOpts.DB_RECORD_TABLE+'&DB_COLUMN='
                                                +aOpts.DB_COLUMN+'&MODULE='
                                                +aOpts.MODULE+'&iRecordID='+iRecordID;
//console.info(Droplet.ThemeUrl);
                                $.ajax({
                                        url: ThemeUrl +"/ajax.php",
                                        type: "POST",
                                        dataType: 'json',
                                        data: sDataString,
                                        success: function(json_respond) {
                                                if(json_respond.success == true) {
//                                                        oElement.animate({opacity: 0.55}), 'fast';
                                                        oElement.attr("src", ThemeUrl + 'img' +"/"+ action +".png");
//                                                        oElement.animate({opacity: 1});
//console.info(action);
                                                } else {
                                                        alert(json_respond.message);
                                                }
                                        }
                                });

                });
        }
})(jQuery);

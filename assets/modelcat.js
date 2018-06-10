/* Nano Templates - https://github.com/trix/nano */

function nano(template, data) {
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
    return (typeof v !== "undefined" && v !== null) ? v : "";
  });
}

/**
 * Modelcat
 */
(function($) {

  /**
   * Run search
   */
  $.fn.modelcatSearch = function() {
    return this.each(function() {
      var $root = $(this);
      
      $.ajax({
        url: modelcat.ajax_url,
        method: "POST",
        data: { "action": "getresults", "foo": "bar" }
      })
        .done(function(response) {
          console.log("DONE", response);
        })
        .fail(function(response) {
          console.log("FAIL", response);
        });
    });
  };

})(jQuery);


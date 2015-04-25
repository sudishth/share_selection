(function($) {
  Drupal.shareSelection = Drupal.shareSelection || {};

  Drupal.shareSelection.onSuccessLinkedin = function(data) {
  }

  Drupal.shareSelection.onErrorLinkedin = function(error) {
    console.log(error);
  }

  Drupal.shareSelection.shareLinkedin = function() {
    // Build the JSON payload containing the content to be shared
    var payload = {
      "comment": Drupal.shareSelection.selectedText.toString(),
      "visibility": {
        "code": "connections-only"
      }
    };

    IN.API.Raw("/people/~/shares?format=json")
      .method("POST")
      .body(JSON.stringify(payload))
      .result(Drupal.shareSelection.onSuccessLinkedin)
      .error(Drupal.shareSelection.onErrorLinkedin);
  }

  Drupal.shareSelection.onLoadLinkedin = function() {
    $('#share-selection-linkedin').mousedown(function(e) {
      if (Drupal.shareSelection.selectedText) {
        IN.User.authorize(Drupal.shareSelection.shareLinkedin);
      };
    });
  }

  Drupal.behaviors.ssLinkedin = {
    attach : function(context, settings) {
      $('body').once('ss-linkedin', function() {
        $.getScript("http://platform.linkedin.com/in.js?async=true", function success() {
          IN.init({
            onLoad: "Drupal.shareSelection.onLoadLinkedin",
            api_key: Drupal.settings.shareSelection.linkedinApikey
          });
        });
      });
    }
  };
})(jQuery);

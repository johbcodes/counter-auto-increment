jQuery(document).ready(function ($) {
  $(".animated-counter").each(function () {
    var $this = $(this);
    var countTo = $this.attr("data-count");

    $({ countNum: 0 }).animate(
      {
        countNum: countTo,
      },
      {
        duration: 2000,
        easing: "swing",
        step: function () {
          $this.text(Math.floor(this.countNum) + "+");
        },
        complete: function () {
          $this.text(this.countNum + "+");
        },
      }
    );
  });
});

$(document).ready(function() {
  $("#search-submit").click(function() {
    $("#loader").show();
    var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
    var str = $(".search_txt").val();
    var str1 = $(".ticket_id").val();
    var str2 = $(".license_txt").val();
    if (
      (str.trim() && str1.trim() && str2.trim()) ||
      (str.trim() && str1.trim()) ||
      (str.trim() && str2.trim()) ||
      (str1.trim() && str2.trim())
    ) {
      $("#loader").hide();
      alert("Please enter (Name or Email) OR Ticket Number OR License Key");
      return;
    }

    if (str.trim()) {
      $.ajax({
        url: "data.php?user=" + str,
        method: "POST",

        success: function(data) {
          $("#loader").hide();
          $("#order_table").html(data);
          var rowCount = $("#table tr").length;
          var count = rowCount - 1;
          $("#record-count").text("Total Support Ticket(s) : " + count);
          var column = $("td:nth-child(1)");
          $(column).hide();
        }
      });
    } else if (str1.trim()) {
      $.ajax({
        url: "data.php?ticket_number=" + str1,
        method: "POST",

        success: function(data) {
          $("#loader").hide();
          $("#order_table").html(data);
          var rowCount = $("#table tr").length;
          var count = rowCount - 1;
          $("#record-count").text("Total Support Ticket(s) : " + count);
          $("td:nth-child(1)").hide();
          var column = $("td:nth-child(1)");
        }
      });
    } else if (str2.trim()) {
      $.ajax({
        url: "data.php?license=" + str2,
        method: "POST",
        success: function(data) {
          $("#loader").hide();
          $("#order_table").html(data);
          var rowCount = $("#table tr").length;
          var count = rowCount - 1;
          $("#record-count").text("Total Support Ticket(s) : " + count);
          $("td:nth-child(1)").hide();
          var column = $("td:nth-child(1)");
        }
      });
    } else {
      $("#loader").hide();
      alert("Please enter valid input");
    }
  });

  $(".search_txt").keypress(function(e) {
    var key = e.which;
    if (key == 13) {
      $("#search-submit").click();
      return false;
    }
  });

  $(".ticket_id").keypress(function(e) {
    var key = e.which;
    if (key == 13) {
      $("#search-submit").click();
      return false;
    }
  });

  $(".license_txt").keypress(function(e) {
    var key = e.which;
    if (key == 13) {
      $("#search-submit").click();
      return false;
    }
  });

  $(document).keyup(function(e) {
    if (e.key === "Escape") {
      document.getElementById("viewModal").style.display = "none";
    }
  });

  $(document).on("click", ".sub", function() {
    $("#loader").show();
    var $row = $(this).closest("tr"); // Find the row
    var $ticketId = $row.find(".nr").text(); // Find the text
    var ticketNum = $row.find(".tn").text();
    var subject = $row.find(".sub").text();
    $(".header-lft").text(ticketNum + " - " + subject);
    if ($ticketId.trim()) {
      $.ajax({
        url: "data.php?ticket_id=" + $ticketId,
        method: "POST",
        success: function(data) {
          $("#loader").hide();
          $(".modal-body").html(data);
          $("#viewModal").show();
        }
      });
    }
  });

  $(document).on("click", "#close-mod", function() {
    $("#viewModal").hide();
  });
});

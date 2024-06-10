   <!-- Core JS -->
   <!-- build:js assets/vendor/js/core.js -->
   <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
   <script src="../../assets/vendor/libs/popper/popper.js"></script>
   <script src="../../assets/vendor/js/bootstrap.js"></script>
   <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
   <script src="../../assets/vendor/libs/hammer/hammer.js"></script>
   <!-- <script src="../../assets/vendor/libs/i18n/i18n.js"></script> -->
   <!-- <script src="../../assets/vendor/libs/typeahead-js/typeahead.js"></script> -->
   <script src="../../assets/vendor/js/menu.js"></script>
   <script src="../../assets/js/main.js"></script>
   <!-- endbuild -->
   <!-- Vendors JS -->
   <script src="../../assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
   <script src="../../assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
   <script src="../../assets/vendor/libs/select2/select2.js"></script>
   <script src="../../assets/vendor/libs/@form-validation/popular.js"></script>
   <script src="../../assets/vendor/libs/@form-validation/bootstrap5.js"></script>
   <script src="../../assets/vendor/libs/@form-validation/auto-focus.js"></script>
   <script src="../../assets/vendor/libs/bloodhound/bloodhound.js"></script>
   <script src="../../assets/vendor/libs/tagify/tagify.js"></script>
   <script src="../../assets/vendor/libs/flatpickr/flatpickr.js"></script>
   <script src="../../assets/vendor/libs/dropzone/dropzone.js"></script>
   <!-- Main JS -->
   <script src="../../assets/js/form-wizard-numbered.js"></script>
   <script src="../../assets/js/pages-account-settings-account.js"></script>
   <script src="../../assets/js/form-wizard-validation.js"></script>
   <script src="../../assets/js/forms-selects.js"></script>
   <script src="../../assets/js/forms-tagify.js"></script>
   <script src="../../assets/js/forms-typeahead.js"></script>
   <script src="../../assets/js/pages-auth.js"></script>
   <script src="../../assets/js/form-validation.js"></script>
   <script src="../../assets/js/ui-toastr.js"></script>
   <script src="../../assets/vendor/libs/toastr/toastr.js"></script>
   <script src="../../assets/vendor/libs/block-ui/block-ui.js"></script>
   <!-- full edit textarea -->
   <script src="../../assets/vendor/libs/quill/katex.js"></script>
   <script src="../../assets/vendor/libs/quill/quill.js"></script>
   <script src="../../assets/js/fulleditor.js"></script>
   <script src="../../assets/vendor/libs/datatables-bs/datatables-bootstrap5.js"></script>
   <script src="../../assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
   <script src="../../assets/vendor/libs/chartjs/chartjs.js"></script>
   <!-- end full edit textare  -->
   <script>
     // Initialize Flatpickr
     flatpickr("#date", {
       minDate: "today",
       dateFormat: "d-M-Y", // Updated to match PHP date format
       defaultDate: "today", // Set default date to today's date
       disable: [
         function(date) {
           // return true to disable
           return (date.getDay() === 0 || date.getDay() === 6);
         }
       ],
     });
     flatpickr("#formValidationDob", {
       enableTime: false, // Set to true if you want to include time selection
       dateFormat: "d-M-Y", // Format of the selected date
     });
     flatpickr("#time", {
       enableTime: true,
       noCalendar: true,
       dateFormat: "H:i" // Format of the selected date
     });
     if (window.history.replaceState) {
       window.history.replaceState(null, null, window.location.href);
     }
   </script>
   <script>
     function resetForm() {
       document.getElementById("filterForm").reset();
     }
   </script>
   <script>
     $(document).ready(function() {
       $('#calendar').flatpickr({
         inline: true,
         enableTime: false,
         dateFormat: "Y-m-d",
       });
     });
   </script>


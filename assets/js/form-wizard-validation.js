'use strict';
!(function () {
  var e = $('.select2'),
    a = $('.selectpicker'),
    i = document.querySelector('#wizard-validation');
  if (null !== i) {
    var t = i.querySelector('#wizard-validation-form');
    const s = t.querySelector('#account-details-validation');
    var o = t.querySelector('#personal-info-validation'),
      n = t.querySelector('#social-links-validation'),
      r = [].slice.call(t.querySelectorAll('.btn-next')),
      t = [].slice.call(t.querySelectorAll('.btn-prev'));
    const l = new Stepper(i, { linear: !0 }),
      d = FormValidation.formValidation(s, {
        fields: {
          formValidationUsername: {
            validators: {
              notEmpty: { message: 'សូមបញ្ចូលឈ្មោះមន្ត្រី' },
              stringLength: {
                min: 6,
                max: 30,
                message: 'ឈ្មោះមន្ត្រីត្រូវចាប់ពី ៦តួអក្សរ ទៅ៣០ តួអក្សរ'
              }
              // regexp: {
              //   regexp: /^[a-zA-Z0-9 ]+$/,
              //   message: 'The name can only consist of alphabetical, number and space'
              // }
            }
          },
          formValidationEmail: {
            validators: {
              notEmpty: { message: 'សូមបញ្ចូលអ៊ីមែល' },
              emailAddress: { message: 'សូមបញ្ចូលទៅតាមទម្រង់នៃអ៊ីមែល' }
            }
          },
          formValidationPass: { validators: { notEmpty: { message: 'សូមបញ្ចូលពាក្យសម្ងាត់' } } },
          formValidationDob: { validators: { notEmpty: { message: 'សូមជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត' } } },
          formValidationConfirmPass: {
            validators: {
              notEmpty: { message: 'សូមបញ្ជាក់ពាក្យសម្ងាត់ម្តងទៀត' },
              identical: {
                compare: function () {
                  return s.querySelector('[name="formValidationPass"]').value;
                },
                message: 'ពាក្យសម្ងាត់ដែលបានបញ្ជាក់មិនដូចគ្នា'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: '', rowSelector: '.col-sm-6' }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        },
        init: e => {
          e.on('plugins.message.placed', function (e) {
            e.element.parentElement.classList.contains('input-group') &&
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          });
        }
      }).on('core.form.valid', function () {
        l.next();
      }),
      m = FormValidation.formValidation(o, {
        fields: {
          formValidationFirstName: { validators: { notEmpty: { message: 'សូមបញ្ចូលគោត្តនាម' } } },
          formValidationLastName: { validators: { notEmpty: { message: 'សូមបញ្ចូលនាម' } } },
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: '', rowSelector: '.col-sm-6' }),
          autoFocus: new FormValidation.plugins.AutoFocus(),
          submitButton: new FormValidation.plugins.SubmitButton()
        }
      }).on('core.form.valid', function () {
        l.next();
      }),
      u =
        (a.length &&
          a.each(function () {
            $(this)
              .selectpicker()
              .on('change', function () {
                m.revalidateField('formValidationLanguage');
              });
          }),
        e.length &&
          e.each(function () {
            var e = $(this);
            e.wrap('<div class="position-relative"></div>'),
              e.select2({ placeholder: 'Select an country', dropdownParent: e.parent() }).on('change', function () {
                m.revalidateField('formValidationCountry');
              });
          }),
        FormValidation.formValidation(n, {
          fields: {
            formValidationTwitter: {
              validators: {
                notEmpty: { message: 'The Twitter URL is required' },
                uri: { message: 'The URL is not proper' }
              }
            },
            formValidationFacebook: {
              validators: {
                notEmpty: { message: 'The Facebook URL is required' },
                uri: { message: 'The URL is not proper' }
              }
            },
            formValidationGoogle: {
              validators: {
                notEmpty: { message: 'The Google URL is required' },
                uri: { message: 'The URL is not proper' }
              }
            },
            formValidationLinkedIn: {
              validators: {
                notEmpty: { message: 'The LinkedIn URL is required' },
                uri: { message: 'The URL is not proper' }
              }
            }
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass: '', rowSelector: '.col-sm-6' }),
            autoFocus: new FormValidation.plugins.AutoFocus(),
            submitButton: new FormValidation.plugins.SubmitButton()
          }
        }).on('core.form.valid', function () {
          alert('Submitted..!!');
        }));
    r.forEach(e => {
      e.addEventListener('click', e => {
        switch (l._currentIndex) {
          case 0:
            d.validate();
            break;
          case 1:
            m.validate();
            break;
          case 2:
            u.validate();
        }
      });
    }),
      t.forEach(e => {
        e.addEventListener('click', e => {
          switch (l._currentIndex) {
            case 2:
            case 1:
              l.previous();
          }
        });
      });
  }
})();

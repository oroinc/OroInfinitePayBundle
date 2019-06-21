define(function(require) {
    'use strict';

    var PaymentAdditionalFieldsComponent;
    var mediator = require('oroui/js/mediator');
    var _ = require('underscore');
    var $ = require('jquery');
    require('jquery.validate');

    var BaseComponent = require('oroui/js/app/components/base/component');

    PaymentAdditionalFieldsComponent = BaseComponent.extend({
        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {Object}
         */
        options: {
            paymentMethod: null,
            selectors: {
                container: '.infinitepay-additional-fields',
                fieldEmail: '[name$="oro_infinite_pay_debtor_data[email]"]',
                fieldLegalform: '[name$="oro_infinite_pay_debtor_data[legal_form]"]',
                inputField: '[name$="[additional_data][__index__]"]'
            }
        },

        /**
         * @inheritDoc
         */
        constructor: function PaymentAdditionalFieldsComponent() {
            PaymentAdditionalFieldsComponent.__super__.constructor.apply(this, arguments);
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = _.extend({}, this.options, options);
            this.$el = $(options._sourceElement);

            mediator.on('checkout:payment:before-form-serialization', this.beforeTransit, this);
            mediator.on('checkout:payment:before-restore-filled-form', this.updateDebtorDataFormIdentifier, this);

            this.validate = this.validate.bind(this);

            this.getForm()
                .on('focusout', 'input,textarea', this.validate)
                .on('change', 'select', this.validate);

            mediator.on('checkout:payment:method:changed', this.onPaymentMethodChanged, this);
            mediator.on('checkout:payment:before-transit', this.validateBeforeTransit, this);
        },

        /**
         * @param {Object} filledForm
         */
        updateDebtorDataFormIdentifier: function(filledForm) {
            this.$el = filledForm;
        },

        /**
         * @param {Object} eventData
         */
        beforeTransit: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                var email = this.getEmailElement().val();
                var legalForm = this.getLegalFormElement().val();

                this.setAdditionalData(email, legalForm);
            }
        },

        /**
         * @param {String} email
         * @param {String} legalForm
         */
        setAdditionalData: function(email, legalForm) {
            var additionalData = {
                email: email,
                legalForm: legalForm
            };

            mediator.trigger('checkout:payment:additional-data:set', JSON.stringify(additionalData));
        },

        /**
         * @param {Object} eventData
         */
        validateBeforeTransit: function(eventData) {
            if (eventData.data.paymentMethod === this.options.paymentMethod) {
                eventData.stopped = !this.validate();
            }
        },

        /**
         * @returns {jQuery|HTMLElement}
         */
        getEmailElement: function() {
            return this.$el.find(this.options.selectors.fieldEmail);
        },

        /**
         * @returns {jQuery|HTMLElement}
         */
        getLegalFormElement: function() {
            return this.$el.find(this.options.selectors.fieldLegalform);
        },

        /**
         * @inheritDoc
         */
        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.getForm()
                .off('focusout', 'input,textarea', this.validate)
                .off('change', 'select', this.validate);

            PaymentAdditionalFieldsComponent.__super__.dispose.call(this);
        },

        /**
         * @returns {jQuery|HTMLElement}
         */
        getForm: function() {
            return $(this.options.selectors.container);
        },

        /**
         * @param {Boolean} state
         */
        setGlobalPaymentValidate: function(state) {
            this.paymentValidationRequiredComponentState = state;
            mediator.trigger('checkout:payment:validate:change', state);
        },

        /**
         * @param {Object} eventData
         */
        onPaymentMethodChanged: function(eventData) {
            if (eventData.paymentMethod === this.options.paymentMethod) {
                this.onCurrentPaymentMethodSelected();
            }
        },

        onCurrentPaymentMethodSelected: function() {
            this.setGlobalPaymentValidate(this.paymentValidationRequiredComponentState);
        },

        /**
         * @param {Object} [event]
         *
         * @returns {Boolean}
         */
        validate: function(event) {
            var appendElement;
            if (event !== undefined && event.target) {
                var element = $(event.target);
                var parentForm = element.closest('form');

                if (parentForm.length) {
                    return element.validate().form();
                }

                appendElement = element.clone();
            } else {
                appendElement = this.getForm().clone();
            }

            var virtualForm = $('<form>');
            virtualForm.append(appendElement);

            var self = this;
            var validator = virtualForm.validate({
                ignore: '', // required to validate all fields in virtual form
                errorPlacement: function(error, element) {
                    var $el = self.getForm().find('#' + $(element).attr('id'));
                    var parentWithValidation = $el.parents('[data-validation]');

                    $el.addClass('error');

                    if (parentWithValidation.length) {
                        error.appendTo(parentWithValidation.first());
                    } else {
                        error.appendTo($el.parent());
                    }
                }
            });

            virtualForm.find('select').each(function(index, item) {
                // set new select to value of old select
                // http://stackoverflow.com/questions/742810/clone-isnt-cloning-select-values
                $(item).val(self.getForm().find('select').eq(index).val());
            });

            // Add validator to form
            $.data(virtualForm, 'validator', validator);

            var errors;

            if (event) {
                errors = $(event.target).parent();
            } else {
                errors = this.getForm();
            }

            errors.find(validator.settings.errorElement + '.' + validator.settings.errorClass).remove();
            errors.parent().find('.error').removeClass('error');

            return validator.form();
        }
    });

    return PaymentAdditionalFieldsComponent;
});


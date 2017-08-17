/**
 * BlueFootSteps
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
var BlueFootSteps = Class.create({

    /**
     * Go to step
     *
     * @param name
     * @param content
     * @returns {*}
     */
    goToStep: function (name, content) {
        if (this.disabled === true) {
            return false;
        }

        var step = this._findStep(name);
        if (content) {
            if (!step) {
                step = this._createStep(name);
            }
            step.update(content);
        }

        this._hideSteps();
        step.show();
        this.currentStep = name;
        this.updateProgress();
        $('bluefoot-steps-errors').hide();
        return step;
    },

    /**
     * Update the progress bar at the top of the migration panel
     */
    updateProgress: function() {
        if (this.currentStep) {
            $$('.bluefoot-steps-progress').each(function (progress) {
                var activeStepSearch = progress.select('li[data-step="' + this.currentStep + '"]');
                if (activeStepSearch.length > 0) {
                    var activeStep = activeStepSearch.first();
                    progress.select('li').each(function (element) {
                        element.removeClassName('step-active').removeClassName('step-inactive');
                        element.stopObserving('click');
                    });
                    activeStep.addClassName('step-active');
                    var previous = activeStep.previousSiblings();
                    if (previous.length > 0) {
                        previous.each(function (element) {
                            element.addClassName('step-inactive');
                            Element.observe(element, 'click', function (event) {
                                Event.stop(event);
                                if (element.readAttribute('data-step')) {
                                    this.goToStep(element.readAttribute('data-step'));
                                }
                            }.bind(this));
                        }.bind(this));
                    }
                }
            }.bind(this));
        }
    },

    /**
     * Validate a form
     *
     * @param form
     * @returns {*}
     */
    validateForm: function (form) {
        var validation = new Validation(form.identify());
        return validation.validate();
    },

    /**
     * Hide all steps
     * @private
     */
    _hideSteps: function () {
        $$('.gene-bluefoot-step').each(function (element) {
            element.hide();
        });
    },

    /**
     * Create a new step within the step system
     *
     * @param name
     * @private
     */
    _createStep: function (name) {
        // Create and insert the new step
        var step = new Element('div', {id: name, class: 'gene-bluefoot-step'});
        $('bluefoot-steps').insert(step);

        // Pull out a direct reference to the step
        step = this._findStep(name);
        this.steps[name] = step;
        return step;
    },

    /**
     * Attempt to find a step within the steps container
     *
     * @param name
     * @returns {*}
     * @private
     */
    _findStep: function (name) {
        if ($$('#bluefoot-steps #' + name).length > 0) {
            return $$('#bluefoot-steps #' + name).first();
        }
        return false;
    },

    /**
     * Perform an Ajax request to the server
     *
     * @param url
     * @param params
     * @param successFn
     * @param method
     * @returns {*}
     */
    request: function (url, params, successFn, method) {
        method = method || 'get';
        params = params || {};
        return new Ajax.Request(
            url,
            {
                method: method,
                dataType: 'json',
                parameters: params,
                onSuccess: function (transport) {
                    if (typeof successFn === 'function') {
                        var data = JSON.parse(transport.responseText);
                        successFn(data, transport);
                    }
                }.bind(this),
                onFailure: function () {
                    this._requestFailed();
                }.bind(this)
            }
        );
    },

    /**
     * What should happen when a request fails?
     *
     * @param message
     * @param errors
     * @returns {boolean}
     */
    requestFailed: function (message, errors) {
        message = message || 'An unknown issue has occurred.';
        errors = errors || [];
        errors.push(message);

        if ($('bluefoot-steps-errors')) {
            $('bluefoot-steps-errors').select('li>ul>li').each(function (element) {
                element.remove();
            });
            errors.forEach(function (error) {
                var errorElement = new Element('li').insert(
                    new Element('span').update(error)
                );
                $('bluefoot-steps-errors').select('ul>li>ul').first().insert({
                    bottom: errorElement
                });
            });
            $('bluefoot-steps-errors').scrollTo();
            $('bluefoot-steps-errors').show();
        } else {
            alert(errors.join("\n"));
        }
        return false;
    }

});
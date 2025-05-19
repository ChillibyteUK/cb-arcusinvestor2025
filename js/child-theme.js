/*!
  * Understrap v1.2.0 (https://understrap.com)
  * Copyright 2013-2025 The Understrap Authors (https://github.com/understrap/understrap/graphs/contributors)
  * Licensed under GPL-3.0 (undefined)
  */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('@popperjs/core')) :
	typeof define === 'function' && define.amd ? define(['exports', '@popperjs/core'], factory) :
	(global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.understrap = {}, global.Popper));
})(this, (function (exports, require$$0) { 'use strict';

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var alert$1 = {exports: {}};

	var baseComponent$1 = {exports: {}};

	var data$1 = {exports: {}};

	/*!
	  * Bootstrap data.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var data = data$1.exports;

	var hasRequiredData;

	function requireData () {
		if (hasRequiredData) return data$1.exports;
		hasRequiredData = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory() ;
			})(data, function () {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap dom/data.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const elementMap = new Map();
			  const data = {
			    set(element, key, instance) {
			      if (!elementMap.has(element)) {
			        elementMap.set(element, new Map());
			      }
			      const instanceMap = elementMap.get(element);

			      // make it clear we only want one instance per element
			      // can be removed later when multiple key/instances are fine to be used
			      if (!instanceMap.has(key) && instanceMap.size !== 0) {
			        // eslint-disable-next-line no-console
			        console.error(`Bootstrap doesn't allow more than one instance per element. Bound instance: ${Array.from(instanceMap.keys())[0]}.`);
			        return;
			      }
			      instanceMap.set(key, instance);
			    },
			    get(element, key) {
			      if (elementMap.has(element)) {
			        return elementMap.get(element).get(key) || null;
			      }
			      return null;
			    },
			    remove(element, key) {
			      if (!elementMap.has(element)) {
			        return;
			      }
			      const instanceMap = elementMap.get(element);
			      instanceMap.delete(key);

			      // free up element references if there are no instances left for an element
			      if (instanceMap.size === 0) {
			        elementMap.delete(element);
			      }
			    }
			  };
			  return data;
			}); 
		} (data$1));
		return data$1.exports;
	}

	var eventHandler$1 = {exports: {}};

	var util$1 = {exports: {}};

	/*!
	  * Bootstrap index.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var util = util$1.exports;

	var hasRequiredUtil;

	function requireUtil () {
		if (hasRequiredUtil) return util$1.exports;
		hasRequiredUtil = 1;
		(function (module, exports) {
			(function (global, factory) {
			  factory(exports) ;
			})(util, function (exports) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/index.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */
			  const MAX_UID = 1000000;
			  const MILLISECONDS_MULTIPLIER = 1000;
			  const TRANSITION_END = 'transitionend';

			  /**
			   * Properly escape IDs selectors to handle weird IDs
			   * @param {string} selector
			   * @returns {string}
			   */
			  const parseSelector = selector => {
			    if (selector && window.CSS && window.CSS.escape) {
			      // document.querySelector needs escaping to handle IDs (html5+) containing for instance /
			      selector = selector.replace(/#([^\s"#']+)/g, (match, id) => `#${CSS.escape(id)}`);
			    }
			    return selector;
			  };

			  // Shout-out Angus Croll (https://goo.gl/pxwQGp)
			  const toType = object => {
			    if (object === null || object === undefined) {
			      return `${object}`;
			    }
			    return Object.prototype.toString.call(object).match(/\s([a-z]+)/i)[1].toLowerCase();
			  };

			  /**
			   * Public Util API
			   */

			  const getUID = prefix => {
			    do {
			      prefix += Math.floor(Math.random() * MAX_UID);
			    } while (document.getElementById(prefix));
			    return prefix;
			  };
			  const getTransitionDurationFromElement = element => {
			    if (!element) {
			      return 0;
			    }

			    // Get transition-duration of the element
			    let {
			      transitionDuration,
			      transitionDelay
			    } = window.getComputedStyle(element);
			    const floatTransitionDuration = Number.parseFloat(transitionDuration);
			    const floatTransitionDelay = Number.parseFloat(transitionDelay);

			    // Return 0 if element or transition duration is not found
			    if (!floatTransitionDuration && !floatTransitionDelay) {
			      return 0;
			    }

			    // If multiple durations are defined, take the first
			    transitionDuration = transitionDuration.split(',')[0];
			    transitionDelay = transitionDelay.split(',')[0];
			    return (Number.parseFloat(transitionDuration) + Number.parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
			  };
			  const triggerTransitionEnd = element => {
			    element.dispatchEvent(new Event(TRANSITION_END));
			  };
			  const isElement = object => {
			    if (!object || typeof object !== 'object') {
			      return false;
			    }
			    if (typeof object.jquery !== 'undefined') {
			      object = object[0];
			    }
			    return typeof object.nodeType !== 'undefined';
			  };
			  const getElement = object => {
			    // it's a jQuery object or a node element
			    if (isElement(object)) {
			      return object.jquery ? object[0] : object;
			    }
			    if (typeof object === 'string' && object.length > 0) {
			      return document.querySelector(parseSelector(object));
			    }
			    return null;
			  };
			  const isVisible = element => {
			    if (!isElement(element) || element.getClientRects().length === 0) {
			      return false;
			    }
			    const elementIsVisible = getComputedStyle(element).getPropertyValue('visibility') === 'visible';
			    // Handle `details` element as its content may falsie appear visible when it is closed
			    const closedDetails = element.closest('details:not([open])');
			    if (!closedDetails) {
			      return elementIsVisible;
			    }
			    if (closedDetails !== element) {
			      const summary = element.closest('summary');
			      if (summary && summary.parentNode !== closedDetails) {
			        return false;
			      }
			      if (summary === null) {
			        return false;
			      }
			    }
			    return elementIsVisible;
			  };
			  const isDisabled = element => {
			    if (!element || element.nodeType !== Node.ELEMENT_NODE) {
			      return true;
			    }
			    if (element.classList.contains('disabled')) {
			      return true;
			    }
			    if (typeof element.disabled !== 'undefined') {
			      return element.disabled;
			    }
			    return element.hasAttribute('disabled') && element.getAttribute('disabled') !== 'false';
			  };
			  const findShadowRoot = element => {
			    if (!document.documentElement.attachShadow) {
			      return null;
			    }

			    // Can find the shadow root otherwise it'll return the document
			    if (typeof element.getRootNode === 'function') {
			      const root = element.getRootNode();
			      return root instanceof ShadowRoot ? root : null;
			    }
			    if (element instanceof ShadowRoot) {
			      return element;
			    }

			    // when we don't find a shadow root
			    if (!element.parentNode) {
			      return null;
			    }
			    return findShadowRoot(element.parentNode);
			  };
			  const noop = () => {};

			  /**
			   * Trick to restart an element's animation
			   *
			   * @param {HTMLElement} element
			   * @return void
			   *
			   * @see https://www.charistheo.io/blog/2021/02/restart-a-css-animation-with-javascript/#restarting-a-css-animation
			   */
			  const reflow = element => {
			    element.offsetHeight; // eslint-disable-line no-unused-expressions
			  };
			  const getjQuery = () => {
			    if (window.jQuery && !document.body.hasAttribute('data-bs-no-jquery')) {
			      return window.jQuery;
			    }
			    return null;
			  };
			  const DOMContentLoadedCallbacks = [];
			  const onDOMContentLoaded = callback => {
			    if (document.readyState === 'loading') {
			      // add listener on the first call when the document is in loading state
			      if (!DOMContentLoadedCallbacks.length) {
			        document.addEventListener('DOMContentLoaded', () => {
			          for (const callback of DOMContentLoadedCallbacks) {
			            callback();
			          }
			        });
			      }
			      DOMContentLoadedCallbacks.push(callback);
			    } else {
			      callback();
			    }
			  };
			  const isRTL = () => document.documentElement.dir === 'rtl';
			  const defineJQueryPlugin = plugin => {
			    onDOMContentLoaded(() => {
			      const $ = getjQuery();
			      /* istanbul ignore if */
			      if ($) {
			        const name = plugin.NAME;
			        const JQUERY_NO_CONFLICT = $.fn[name];
			        $.fn[name] = plugin.jQueryInterface;
			        $.fn[name].Constructor = plugin;
			        $.fn[name].noConflict = () => {
			          $.fn[name] = JQUERY_NO_CONFLICT;
			          return plugin.jQueryInterface;
			        };
			      }
			    });
			  };
			  const execute = (possibleCallback, args = [], defaultValue = possibleCallback) => {
			    return typeof possibleCallback === 'function' ? possibleCallback(...args) : defaultValue;
			  };
			  const executeAfterTransition = (callback, transitionElement, waitForTransition = true) => {
			    if (!waitForTransition) {
			      execute(callback);
			      return;
			    }
			    const durationPadding = 5;
			    const emulatedDuration = getTransitionDurationFromElement(transitionElement) + durationPadding;
			    let called = false;
			    const handler = ({
			      target
			    }) => {
			      if (target !== transitionElement) {
			        return;
			      }
			      called = true;
			      transitionElement.removeEventListener(TRANSITION_END, handler);
			      execute(callback);
			    };
			    transitionElement.addEventListener(TRANSITION_END, handler);
			    setTimeout(() => {
			      if (!called) {
			        triggerTransitionEnd(transitionElement);
			      }
			    }, emulatedDuration);
			  };

			  /**
			   * Return the previous/next element of a list.
			   *
			   * @param {array} list    The list of elements
			   * @param activeElement   The active element
			   * @param shouldGetNext   Choose to get next or previous element
			   * @param isCycleAllowed
			   * @return {Element|elem} The proper element
			   */
			  const getNextActiveElement = (list, activeElement, shouldGetNext, isCycleAllowed) => {
			    const listLength = list.length;
			    let index = list.indexOf(activeElement);

			    // if the element does not exist in the list return an element
			    // depending on the direction and if cycle is allowed
			    if (index === -1) {
			      return !shouldGetNext && isCycleAllowed ? list[listLength - 1] : list[0];
			    }
			    index += shouldGetNext ? 1 : -1;
			    if (isCycleAllowed) {
			      index = (index + listLength) % listLength;
			    }
			    return list[Math.max(0, Math.min(index, listLength - 1))];
			  };
			  exports.defineJQueryPlugin = defineJQueryPlugin;
			  exports.execute = execute;
			  exports.executeAfterTransition = executeAfterTransition;
			  exports.findShadowRoot = findShadowRoot;
			  exports.getElement = getElement;
			  exports.getNextActiveElement = getNextActiveElement;
			  exports.getTransitionDurationFromElement = getTransitionDurationFromElement;
			  exports.getUID = getUID;
			  exports.getjQuery = getjQuery;
			  exports.isDisabled = isDisabled;
			  exports.isElement = isElement;
			  exports.isRTL = isRTL;
			  exports.isVisible = isVisible;
			  exports.noop = noop;
			  exports.onDOMContentLoaded = onDOMContentLoaded;
			  exports.parseSelector = parseSelector;
			  exports.reflow = reflow;
			  exports.toType = toType;
			  exports.triggerTransitionEnd = triggerTransitionEnd;
			  Object.defineProperty(exports, Symbol.toStringTag, {
			    value: 'Module'
			  });
			}); 
		} (util$1, util$1.exports));
		return util$1.exports;
	}

	/*!
	  * Bootstrap event-handler.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var eventHandler = eventHandler$1.exports;

	var hasRequiredEventHandler;

	function requireEventHandler () {
		if (hasRequiredEventHandler) return eventHandler$1.exports;
		hasRequiredEventHandler = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireUtil()) ;
			})(eventHandler, function (index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap dom/event-handler.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const namespaceRegex = /[^.]*(?=\..*)\.|.*/;
			  const stripNameRegex = /\..*/;
			  const stripUidRegex = /::\d+$/;
			  const eventRegistry = {}; // Events storage
			  let uidEvent = 1;
			  const customEvents = {
			    mouseenter: 'mouseover',
			    mouseleave: 'mouseout'
			  };
			  const nativeEvents = new Set(['click', 'dblclick', 'mouseup', 'mousedown', 'contextmenu', 'mousewheel', 'DOMMouseScroll', 'mouseover', 'mouseout', 'mousemove', 'selectstart', 'selectend', 'keydown', 'keypress', 'keyup', 'orientationchange', 'touchstart', 'touchmove', 'touchend', 'touchcancel', 'pointerdown', 'pointermove', 'pointerup', 'pointerleave', 'pointercancel', 'gesturestart', 'gesturechange', 'gestureend', 'focus', 'blur', 'change', 'reset', 'select', 'submit', 'focusin', 'focusout', 'load', 'unload', 'beforeunload', 'resize', 'move', 'DOMContentLoaded', 'readystatechange', 'error', 'abort', 'scroll']);

			  /**
			   * Private methods
			   */

			  function makeEventUid(element, uid) {
			    return uid && `${uid}::${uidEvent++}` || element.uidEvent || uidEvent++;
			  }
			  function getElementEvents(element) {
			    const uid = makeEventUid(element);
			    element.uidEvent = uid;
			    eventRegistry[uid] = eventRegistry[uid] || {};
			    return eventRegistry[uid];
			  }
			  function bootstrapHandler(element, fn) {
			    return function handler(event) {
			      hydrateObj(event, {
			        delegateTarget: element
			      });
			      if (handler.oneOff) {
			        EventHandler.off(element, event.type, fn);
			      }
			      return fn.apply(element, [event]);
			    };
			  }
			  function bootstrapDelegationHandler(element, selector, fn) {
			    return function handler(event) {
			      const domElements = element.querySelectorAll(selector);
			      for (let {
			        target
			      } = event; target && target !== this; target = target.parentNode) {
			        for (const domElement of domElements) {
			          if (domElement !== target) {
			            continue;
			          }
			          hydrateObj(event, {
			            delegateTarget: target
			          });
			          if (handler.oneOff) {
			            EventHandler.off(element, event.type, selector, fn);
			          }
			          return fn.apply(target, [event]);
			        }
			      }
			    };
			  }
			  function findHandler(events, callable, delegationSelector = null) {
			    return Object.values(events).find(event => event.callable === callable && event.delegationSelector === delegationSelector);
			  }
			  function normalizeParameters(originalTypeEvent, handler, delegationFunction) {
			    const isDelegated = typeof handler === 'string';
			    // TODO: tooltip passes `false` instead of selector, so we need to check
			    const callable = isDelegated ? delegationFunction : handler || delegationFunction;
			    let typeEvent = getTypeEvent(originalTypeEvent);
			    if (!nativeEvents.has(typeEvent)) {
			      typeEvent = originalTypeEvent;
			    }
			    return [isDelegated, callable, typeEvent];
			  }
			  function addHandler(element, originalTypeEvent, handler, delegationFunction, oneOff) {
			    if (typeof originalTypeEvent !== 'string' || !element) {
			      return;
			    }
			    let [isDelegated, callable, typeEvent] = normalizeParameters(originalTypeEvent, handler, delegationFunction);

			    // in case of mouseenter or mouseleave wrap the handler within a function that checks for its DOM position
			    // this prevents the handler from being dispatched the same way as mouseover or mouseout does
			    if (originalTypeEvent in customEvents) {
			      const wrapFunction = fn => {
			        return function (event) {
			          if (!event.relatedTarget || event.relatedTarget !== event.delegateTarget && !event.delegateTarget.contains(event.relatedTarget)) {
			            return fn.call(this, event);
			          }
			        };
			      };
			      callable = wrapFunction(callable);
			    }
			    const events = getElementEvents(element);
			    const handlers = events[typeEvent] || (events[typeEvent] = {});
			    const previousFunction = findHandler(handlers, callable, isDelegated ? handler : null);
			    if (previousFunction) {
			      previousFunction.oneOff = previousFunction.oneOff && oneOff;
			      return;
			    }
			    const uid = makeEventUid(callable, originalTypeEvent.replace(namespaceRegex, ''));
			    const fn = isDelegated ? bootstrapDelegationHandler(element, handler, callable) : bootstrapHandler(element, callable);
			    fn.delegationSelector = isDelegated ? handler : null;
			    fn.callable = callable;
			    fn.oneOff = oneOff;
			    fn.uidEvent = uid;
			    handlers[uid] = fn;
			    element.addEventListener(typeEvent, fn, isDelegated);
			  }
			  function removeHandler(element, events, typeEvent, handler, delegationSelector) {
			    const fn = findHandler(events[typeEvent], handler, delegationSelector);
			    if (!fn) {
			      return;
			    }
			    element.removeEventListener(typeEvent, fn, Boolean(delegationSelector));
			    delete events[typeEvent][fn.uidEvent];
			  }
			  function removeNamespacedHandlers(element, events, typeEvent, namespace) {
			    const storeElementEvent = events[typeEvent] || {};
			    for (const [handlerKey, event] of Object.entries(storeElementEvent)) {
			      if (handlerKey.includes(namespace)) {
			        removeHandler(element, events, typeEvent, event.callable, event.delegationSelector);
			      }
			    }
			  }
			  function getTypeEvent(event) {
			    // allow to get the native events from namespaced events ('click.bs.button' --> 'click')
			    event = event.replace(stripNameRegex, '');
			    return customEvents[event] || event;
			  }
			  const EventHandler = {
			    on(element, event, handler, delegationFunction) {
			      addHandler(element, event, handler, delegationFunction, false);
			    },
			    one(element, event, handler, delegationFunction) {
			      addHandler(element, event, handler, delegationFunction, true);
			    },
			    off(element, originalTypeEvent, handler, delegationFunction) {
			      if (typeof originalTypeEvent !== 'string' || !element) {
			        return;
			      }
			      const [isDelegated, callable, typeEvent] = normalizeParameters(originalTypeEvent, handler, delegationFunction);
			      const inNamespace = typeEvent !== originalTypeEvent;
			      const events = getElementEvents(element);
			      const storeElementEvent = events[typeEvent] || {};
			      const isNamespace = originalTypeEvent.startsWith('.');
			      if (typeof callable !== 'undefined') {
			        // Simplest case: handler is passed, remove that listener ONLY.
			        if (!Object.keys(storeElementEvent).length) {
			          return;
			        }
			        removeHandler(element, events, typeEvent, callable, isDelegated ? handler : null);
			        return;
			      }
			      if (isNamespace) {
			        for (const elementEvent of Object.keys(events)) {
			          removeNamespacedHandlers(element, events, elementEvent, originalTypeEvent.slice(1));
			        }
			      }
			      for (const [keyHandlers, event] of Object.entries(storeElementEvent)) {
			        const handlerKey = keyHandlers.replace(stripUidRegex, '');
			        if (!inNamespace || originalTypeEvent.includes(handlerKey)) {
			          removeHandler(element, events, typeEvent, event.callable, event.delegationSelector);
			        }
			      }
			    },
			    trigger(element, event, args) {
			      if (typeof event !== 'string' || !element) {
			        return null;
			      }
			      const $ = index_js.getjQuery();
			      const typeEvent = getTypeEvent(event);
			      const inNamespace = event !== typeEvent;
			      let jQueryEvent = null;
			      let bubbles = true;
			      let nativeDispatch = true;
			      let defaultPrevented = false;
			      if (inNamespace && $) {
			        jQueryEvent = $.Event(event, args);
			        $(element).trigger(jQueryEvent);
			        bubbles = !jQueryEvent.isPropagationStopped();
			        nativeDispatch = !jQueryEvent.isImmediatePropagationStopped();
			        defaultPrevented = jQueryEvent.isDefaultPrevented();
			      }
			      const evt = hydrateObj(new Event(event, {
			        bubbles,
			        cancelable: true
			      }), args);
			      if (defaultPrevented) {
			        evt.preventDefault();
			      }
			      if (nativeDispatch) {
			        element.dispatchEvent(evt);
			      }
			      if (evt.defaultPrevented && jQueryEvent) {
			        jQueryEvent.preventDefault();
			      }
			      return evt;
			    }
			  };
			  function hydrateObj(obj, meta = {}) {
			    for (const [key, value] of Object.entries(meta)) {
			      try {
			        obj[key] = value;
			      } catch (_unused) {
			        Object.defineProperty(obj, key, {
			          configurable: true,
			          get() {
			            return value;
			          }
			        });
			      }
			    }
			    return obj;
			  }
			  return EventHandler;
			}); 
		} (eventHandler$1));
		return eventHandler$1.exports;
	}

	var config$1 = {exports: {}};

	var manipulator$1 = {exports: {}};

	/*!
	  * Bootstrap manipulator.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var manipulator = manipulator$1.exports;

	var hasRequiredManipulator;

	function requireManipulator () {
		if (hasRequiredManipulator) return manipulator$1.exports;
		hasRequiredManipulator = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory() ;
			})(manipulator, function () {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap dom/manipulator.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */
			  function normalizeData(value) {
			    if (value === 'true') {
			      return true;
			    }
			    if (value === 'false') {
			      return false;
			    }
			    if (value === Number(value).toString()) {
			      return Number(value);
			    }
			    if (value === '' || value === 'null') {
			      return null;
			    }
			    if (typeof value !== 'string') {
			      return value;
			    }
			    try {
			      return JSON.parse(decodeURIComponent(value));
			    } catch (_unused) {
			      return value;
			    }
			  }
			  function normalizeDataKey(key) {
			    return key.replace(/[A-Z]/g, chr => `-${chr.toLowerCase()}`);
			  }
			  const Manipulator = {
			    setDataAttribute(element, key, value) {
			      element.setAttribute(`data-bs-${normalizeDataKey(key)}`, value);
			    },
			    removeDataAttribute(element, key) {
			      element.removeAttribute(`data-bs-${normalizeDataKey(key)}`);
			    },
			    getDataAttributes(element) {
			      if (!element) {
			        return {};
			      }
			      const attributes = {};
			      const bsKeys = Object.keys(element.dataset).filter(key => key.startsWith('bs') && !key.startsWith('bsConfig'));
			      for (const key of bsKeys) {
			        let pureKey = key.replace(/^bs/, '');
			        pureKey = pureKey.charAt(0).toLowerCase() + pureKey.slice(1, pureKey.length);
			        attributes[pureKey] = normalizeData(element.dataset[key]);
			      }
			      return attributes;
			    },
			    getDataAttribute(element, key) {
			      return normalizeData(element.getAttribute(`data-bs-${normalizeDataKey(key)}`));
			    }
			  };
			  return Manipulator;
			}); 
		} (manipulator$1));
		return manipulator$1.exports;
	}

	/*!
	  * Bootstrap config.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var config = config$1.exports;

	var hasRequiredConfig;

	function requireConfig () {
		if (hasRequiredConfig) return config$1.exports;
		hasRequiredConfig = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireManipulator(), requireUtil()) ;
			})(config, function (Manipulator, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/config.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Class definition
			   */
			  class Config {
			    // Getters
			    static get Default() {
			      return {};
			    }
			    static get DefaultType() {
			      return {};
			    }
			    static get NAME() {
			      throw new Error('You have to implement the static method "NAME", for each component!');
			    }
			    _getConfig(config) {
			      config = this._mergeConfigObj(config);
			      config = this._configAfterMerge(config);
			      this._typeCheckConfig(config);
			      return config;
			    }
			    _configAfterMerge(config) {
			      return config;
			    }
			    _mergeConfigObj(config, element) {
			      const jsonConfig = index_js.isElement(element) ? Manipulator.getDataAttribute(element, 'config') : {}; // try to parse

			      return {
			        ...this.constructor.Default,
			        ...(typeof jsonConfig === 'object' ? jsonConfig : {}),
			        ...(index_js.isElement(element) ? Manipulator.getDataAttributes(element) : {}),
			        ...(typeof config === 'object' ? config : {})
			      };
			    }
			    _typeCheckConfig(config, configTypes = this.constructor.DefaultType) {
			      for (const [property, expectedTypes] of Object.entries(configTypes)) {
			        const value = config[property];
			        const valueType = index_js.isElement(value) ? 'element' : index_js.toType(value);
			        if (!new RegExp(expectedTypes).test(valueType)) {
			          throw new TypeError(`${this.constructor.NAME.toUpperCase()}: Option "${property}" provided type "${valueType}" but expected type "${expectedTypes}".`);
			        }
			      }
			    }
			  }
			  return Config;
			}); 
		} (config$1));
		return config$1.exports;
	}

	/*!
	  * Bootstrap base-component.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var baseComponent = baseComponent$1.exports;

	var hasRequiredBaseComponent;

	function requireBaseComponent () {
		if (hasRequiredBaseComponent) return baseComponent$1.exports;
		hasRequiredBaseComponent = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireData(), requireEventHandler(), requireConfig(), requireUtil()) ;
			})(baseComponent, function (Data, EventHandler, Config, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap base-component.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const VERSION = '5.3.3';

			  /**
			   * Class definition
			   */

			  class BaseComponent extends Config {
			    constructor(element, config) {
			      super();
			      element = index_js.getElement(element);
			      if (!element) {
			        return;
			      }
			      this._element = element;
			      this._config = this._getConfig(config);
			      Data.set(this._element, this.constructor.DATA_KEY, this);
			    }

			    // Public
			    dispose() {
			      Data.remove(this._element, this.constructor.DATA_KEY);
			      EventHandler.off(this._element, this.constructor.EVENT_KEY);
			      for (const propertyName of Object.getOwnPropertyNames(this)) {
			        this[propertyName] = null;
			      }
			    }
			    _queueCallback(callback, element, isAnimated = true) {
			      index_js.executeAfterTransition(callback, element, isAnimated);
			    }
			    _getConfig(config) {
			      config = this._mergeConfigObj(config, this._element);
			      config = this._configAfterMerge(config);
			      this._typeCheckConfig(config);
			      return config;
			    }

			    // Static
			    static getInstance(element) {
			      return Data.get(index_js.getElement(element), this.DATA_KEY);
			    }
			    static getOrCreateInstance(element, config = {}) {
			      return this.getInstance(element) || new this(element, typeof config === 'object' ? config : null);
			    }
			    static get VERSION() {
			      return VERSION;
			    }
			    static get DATA_KEY() {
			      return `bs.${this.NAME}`;
			    }
			    static get EVENT_KEY() {
			      return `.${this.DATA_KEY}`;
			    }
			    static eventName(name) {
			      return `${name}${this.EVENT_KEY}`;
			    }
			  }
			  return BaseComponent;
			}); 
		} (baseComponent$1));
		return baseComponent$1.exports;
	}

	var componentFunctions$1 = {exports: {}};

	var selectorEngine$1 = {exports: {}};

	/*!
	  * Bootstrap selector-engine.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var selectorEngine = selectorEngine$1.exports;

	var hasRequiredSelectorEngine;

	function requireSelectorEngine () {
		if (hasRequiredSelectorEngine) return selectorEngine$1.exports;
		hasRequiredSelectorEngine = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireUtil()) ;
			})(selectorEngine, function (index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap dom/selector-engine.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */
			  const getSelector = element => {
			    let selector = element.getAttribute('data-bs-target');
			    if (!selector || selector === '#') {
			      let hrefAttribute = element.getAttribute('href');

			      // The only valid content that could double as a selector are IDs or classes,
			      // so everything starting with `#` or `.`. If a "real" URL is used as the selector,
			      // `document.querySelector` will rightfully complain it is invalid.
			      // See https://github.com/twbs/bootstrap/issues/32273
			      if (!hrefAttribute || !hrefAttribute.includes('#') && !hrefAttribute.startsWith('.')) {
			        return null;
			      }

			      // Just in case some CMS puts out a full URL with the anchor appended
			      if (hrefAttribute.includes('#') && !hrefAttribute.startsWith('#')) {
			        hrefAttribute = `#${hrefAttribute.split('#')[1]}`;
			      }
			      selector = hrefAttribute && hrefAttribute !== '#' ? hrefAttribute.trim() : null;
			    }
			    return selector ? selector.split(',').map(sel => index_js.parseSelector(sel)).join(',') : null;
			  };
			  const SelectorEngine = {
			    find(selector, element = document.documentElement) {
			      return [].concat(...Element.prototype.querySelectorAll.call(element, selector));
			    },
			    findOne(selector, element = document.documentElement) {
			      return Element.prototype.querySelector.call(element, selector);
			    },
			    children(element, selector) {
			      return [].concat(...element.children).filter(child => child.matches(selector));
			    },
			    parents(element, selector) {
			      const parents = [];
			      let ancestor = element.parentNode.closest(selector);
			      while (ancestor) {
			        parents.push(ancestor);
			        ancestor = ancestor.parentNode.closest(selector);
			      }
			      return parents;
			    },
			    prev(element, selector) {
			      let previous = element.previousElementSibling;
			      while (previous) {
			        if (previous.matches(selector)) {
			          return [previous];
			        }
			        previous = previous.previousElementSibling;
			      }
			      return [];
			    },
			    // TODO: this is now unused; remove later along with prev()
			    next(element, selector) {
			      let next = element.nextElementSibling;
			      while (next) {
			        if (next.matches(selector)) {
			          return [next];
			        }
			        next = next.nextElementSibling;
			      }
			      return [];
			    },
			    focusableChildren(element) {
			      const focusables = ['a', 'button', 'input', 'textarea', 'select', 'details', '[tabindex]', '[contenteditable="true"]'].map(selector => `${selector}:not([tabindex^="-"])`).join(',');
			      return this.find(focusables, element).filter(el => !index_js.isDisabled(el) && index_js.isVisible(el));
			    },
			    getSelectorFromElement(element) {
			      const selector = getSelector(element);
			      if (selector) {
			        return SelectorEngine.findOne(selector) ? selector : null;
			      }
			      return null;
			    },
			    getElementFromSelector(element) {
			      const selector = getSelector(element);
			      return selector ? SelectorEngine.findOne(selector) : null;
			    },
			    getMultipleElementsFromSelector(element) {
			      const selector = getSelector(element);
			      return selector ? SelectorEngine.find(selector) : [];
			    }
			  };
			  return SelectorEngine;
			}); 
		} (selectorEngine$1));
		return selectorEngine$1.exports;
	}

	/*!
	  * Bootstrap component-functions.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var componentFunctions = componentFunctions$1.exports;

	var hasRequiredComponentFunctions;

	function requireComponentFunctions () {
		if (hasRequiredComponentFunctions) return componentFunctions$1.exports;
		hasRequiredComponentFunctions = 1;
		(function (module, exports) {
			(function (global, factory) {
			  factory(exports, requireEventHandler(), requireSelectorEngine(), requireUtil()) ;
			})(componentFunctions, function (exports, EventHandler, SelectorEngine, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/component-functions.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */
			  const enableDismissTrigger = (component, method = 'hide') => {
			    const clickEvent = `click.dismiss${component.EVENT_KEY}`;
			    const name = component.NAME;
			    EventHandler.on(document, clickEvent, `[data-bs-dismiss="${name}"]`, function (event) {
			      if (['A', 'AREA'].includes(this.tagName)) {
			        event.preventDefault();
			      }
			      if (index_js.isDisabled(this)) {
			        return;
			      }
			      const target = SelectorEngine.getElementFromSelector(this) || this.closest(`.${name}`);
			      const instance = component.getOrCreateInstance(target);

			      // Method argument is left, for Alert and only, as it doesn't implement the 'hide' method
			      instance[method]();
			    });
			  };
			  exports.enableDismissTrigger = enableDismissTrigger;
			  Object.defineProperty(exports, Symbol.toStringTag, {
			    value: 'Module'
			  });
			}); 
		} (componentFunctions$1, componentFunctions$1.exports));
		return componentFunctions$1.exports;
	}

	/*!
	  * Bootstrap alert.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var alert = alert$1.exports;

	var hasRequiredAlert;

	function requireAlert () {
		if (hasRequiredAlert) return alert$1.exports;
		hasRequiredAlert = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireComponentFunctions(), requireUtil()) ;
			})(alert, function (BaseComponent, EventHandler, componentFunctions_js, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap alert.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'alert';
			  const DATA_KEY = 'bs.alert';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const EVENT_CLOSE = `close${EVENT_KEY}`;
			  const EVENT_CLOSED = `closed${EVENT_KEY}`;
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_SHOW = 'show';

			  /**
			   * Class definition
			   */

			  class Alert extends BaseComponent {
			    // Getters
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    close() {
			      const closeEvent = EventHandler.trigger(this._element, EVENT_CLOSE);
			      if (closeEvent.defaultPrevented) {
			        return;
			      }
			      this._element.classList.remove(CLASS_NAME_SHOW);
			      const isAnimated = this._element.classList.contains(CLASS_NAME_FADE);
			      this._queueCallback(() => this._destroyElement(), this._element, isAnimated);
			    }

			    // Private
			    _destroyElement() {
			      this._element.remove();
			      EventHandler.trigger(this._element, EVENT_CLOSED);
			      this.dispose();
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Alert.getOrCreateInstance(this);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config](this);
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  componentFunctions_js.enableDismissTrigger(Alert, 'close');

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Alert);
			  return Alert;
			}); 
		} (alert$1));
		return alert$1.exports;
	}

	var alertExports = requireAlert();
	var Alert = /*@__PURE__*/getDefaultExportFromCjs(alertExports);

	var button$2 = {exports: {}};

	/*!
	  * Bootstrap button.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var button$1 = button$2.exports;

	var hasRequiredButton;

	function requireButton () {
		if (hasRequiredButton) return button$2.exports;
		hasRequiredButton = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireUtil()) ;
			})(button$1, function (BaseComponent, EventHandler, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap button.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'button';
			  const DATA_KEY = 'bs.button';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const CLASS_NAME_ACTIVE = 'active';
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="button"]';
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;

			  /**
			   * Class definition
			   */

			  class Button extends BaseComponent {
			    // Getters
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    toggle() {
			      // Toggle class and sync the `aria-pressed` attribute with the return value of the `.toggle()` method
			      this._element.setAttribute('aria-pressed', this._element.classList.toggle(CLASS_NAME_ACTIVE));
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Button.getOrCreateInstance(this);
			        if (config === 'toggle') {
			          data[config]();
			        }
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, event => {
			    event.preventDefault();
			    const button = event.target.closest(SELECTOR_DATA_TOGGLE);
			    const data = Button.getOrCreateInstance(button);
			    data.toggle();
			  });

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Button);
			  return Button;
			}); 
		} (button$2));
		return button$2.exports;
	}

	var buttonExports = requireButton();
	var button = /*@__PURE__*/getDefaultExportFromCjs(buttonExports);

	var carousel$2 = {exports: {}};

	var swipe$1 = {exports: {}};

	/*!
	  * Bootstrap swipe.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var swipe = swipe$1.exports;

	var hasRequiredSwipe;

	function requireSwipe () {
		if (hasRequiredSwipe) return swipe$1.exports;
		hasRequiredSwipe = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireEventHandler(), requireConfig(), requireUtil()) ;
			})(swipe, function (EventHandler, Config, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/swipe.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'swipe';
			  const EVENT_KEY = '.bs.swipe';
			  const EVENT_TOUCHSTART = `touchstart${EVENT_KEY}`;
			  const EVENT_TOUCHMOVE = `touchmove${EVENT_KEY}`;
			  const EVENT_TOUCHEND = `touchend${EVENT_KEY}`;
			  const EVENT_POINTERDOWN = `pointerdown${EVENT_KEY}`;
			  const EVENT_POINTERUP = `pointerup${EVENT_KEY}`;
			  const POINTER_TYPE_TOUCH = 'touch';
			  const POINTER_TYPE_PEN = 'pen';
			  const CLASS_NAME_POINTER_EVENT = 'pointer-event';
			  const SWIPE_THRESHOLD = 40;
			  const Default = {
			    endCallback: null,
			    leftCallback: null,
			    rightCallback: null
			  };
			  const DefaultType = {
			    endCallback: '(function|null)',
			    leftCallback: '(function|null)',
			    rightCallback: '(function|null)'
			  };

			  /**
			   * Class definition
			   */

			  class Swipe extends Config {
			    constructor(element, config) {
			      super();
			      this._element = element;
			      if (!element || !Swipe.isSupported()) {
			        return;
			      }
			      this._config = this._getConfig(config);
			      this._deltaX = 0;
			      this._supportPointerEvents = Boolean(window.PointerEvent);
			      this._initEvents();
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    dispose() {
			      EventHandler.off(this._element, EVENT_KEY);
			    }

			    // Private
			    _start(event) {
			      if (!this._supportPointerEvents) {
			        this._deltaX = event.touches[0].clientX;
			        return;
			      }
			      if (this._eventIsPointerPenTouch(event)) {
			        this._deltaX = event.clientX;
			      }
			    }
			    _end(event) {
			      if (this._eventIsPointerPenTouch(event)) {
			        this._deltaX = event.clientX - this._deltaX;
			      }
			      this._handleSwipe();
			      index_js.execute(this._config.endCallback);
			    }
			    _move(event) {
			      this._deltaX = event.touches && event.touches.length > 1 ? 0 : event.touches[0].clientX - this._deltaX;
			    }
			    _handleSwipe() {
			      const absDeltaX = Math.abs(this._deltaX);
			      if (absDeltaX <= SWIPE_THRESHOLD) {
			        return;
			      }
			      const direction = absDeltaX / this._deltaX;
			      this._deltaX = 0;
			      if (!direction) {
			        return;
			      }
			      index_js.execute(direction > 0 ? this._config.rightCallback : this._config.leftCallback);
			    }
			    _initEvents() {
			      if (this._supportPointerEvents) {
			        EventHandler.on(this._element, EVENT_POINTERDOWN, event => this._start(event));
			        EventHandler.on(this._element, EVENT_POINTERUP, event => this._end(event));
			        this._element.classList.add(CLASS_NAME_POINTER_EVENT);
			      } else {
			        EventHandler.on(this._element, EVENT_TOUCHSTART, event => this._start(event));
			        EventHandler.on(this._element, EVENT_TOUCHMOVE, event => this._move(event));
			        EventHandler.on(this._element, EVENT_TOUCHEND, event => this._end(event));
			      }
			    }
			    _eventIsPointerPenTouch(event) {
			      return this._supportPointerEvents && (event.pointerType === POINTER_TYPE_PEN || event.pointerType === POINTER_TYPE_TOUCH);
			    }

			    // Static
			    static isSupported() {
			      return 'ontouchstart' in document.documentElement || navigator.maxTouchPoints > 0;
			    }
			  }
			  return Swipe;
			}); 
		} (swipe$1));
		return swipe$1.exports;
	}

	/*!
	  * Bootstrap carousel.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var carousel$1 = carousel$2.exports;

	var hasRequiredCarousel;

	function requireCarousel () {
		if (hasRequiredCarousel) return carousel$2.exports;
		hasRequiredCarousel = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireManipulator(), requireSelectorEngine(), requireUtil(), requireSwipe()) ;
			})(carousel$1, function (BaseComponent, EventHandler, Manipulator, SelectorEngine, index_js, Swipe) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap carousel.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'carousel';
			  const DATA_KEY = 'bs.carousel';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const ARROW_LEFT_KEY = 'ArrowLeft';
			  const ARROW_RIGHT_KEY = 'ArrowRight';
			  const TOUCHEVENT_COMPAT_WAIT = 500; // Time for mouse compat events to fire after touch

			  const ORDER_NEXT = 'next';
			  const ORDER_PREV = 'prev';
			  const DIRECTION_LEFT = 'left';
			  const DIRECTION_RIGHT = 'right';
			  const EVENT_SLIDE = `slide${EVENT_KEY}`;
			  const EVENT_SLID = `slid${EVENT_KEY}`;
			  const EVENT_KEYDOWN = `keydown${EVENT_KEY}`;
			  const EVENT_MOUSEENTER = `mouseenter${EVENT_KEY}`;
			  const EVENT_MOUSELEAVE = `mouseleave${EVENT_KEY}`;
			  const EVENT_DRAG_START = `dragstart${EVENT_KEY}`;
			  const EVENT_LOAD_DATA_API = `load${EVENT_KEY}${DATA_API_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
			  const CLASS_NAME_CAROUSEL = 'carousel';
			  const CLASS_NAME_ACTIVE = 'active';
			  const CLASS_NAME_SLIDE = 'slide';
			  const CLASS_NAME_END = 'carousel-item-end';
			  const CLASS_NAME_START = 'carousel-item-start';
			  const CLASS_NAME_NEXT = 'carousel-item-next';
			  const CLASS_NAME_PREV = 'carousel-item-prev';
			  const SELECTOR_ACTIVE = '.active';
			  const SELECTOR_ITEM = '.carousel-item';
			  const SELECTOR_ACTIVE_ITEM = SELECTOR_ACTIVE + SELECTOR_ITEM;
			  const SELECTOR_ITEM_IMG = '.carousel-item img';
			  const SELECTOR_INDICATORS = '.carousel-indicators';
			  const SELECTOR_DATA_SLIDE = '[data-bs-slide], [data-bs-slide-to]';
			  const SELECTOR_DATA_RIDE = '[data-bs-ride="carousel"]';
			  const KEY_TO_DIRECTION = {
			    [ARROW_LEFT_KEY]: DIRECTION_RIGHT,
			    [ARROW_RIGHT_KEY]: DIRECTION_LEFT
			  };
			  const Default = {
			    interval: 5000,
			    keyboard: true,
			    pause: 'hover',
			    ride: false,
			    touch: true,
			    wrap: true
			  };
			  const DefaultType = {
			    interval: '(number|boolean)',
			    // TODO:v6 remove boolean support
			    keyboard: 'boolean',
			    pause: '(string|boolean)',
			    ride: '(boolean|string)',
			    touch: 'boolean',
			    wrap: 'boolean'
			  };

			  /**
			   * Class definition
			   */

			  class Carousel extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._interval = null;
			      this._activeElement = null;
			      this._isSliding = false;
			      this.touchTimeout = null;
			      this._swipeHelper = null;
			      this._indicatorsElement = SelectorEngine.findOne(SELECTOR_INDICATORS, this._element);
			      this._addEventListeners();
			      if (this._config.ride === CLASS_NAME_CAROUSEL) {
			        this.cycle();
			      }
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    next() {
			      this._slide(ORDER_NEXT);
			    }
			    nextWhenVisible() {
			      // FIXME TODO use `document.visibilityState`
			      // Don't call next when the page isn't visible
			      // or the carousel or its parent isn't visible
			      if (!document.hidden && index_js.isVisible(this._element)) {
			        this.next();
			      }
			    }
			    prev() {
			      this._slide(ORDER_PREV);
			    }
			    pause() {
			      if (this._isSliding) {
			        index_js.triggerTransitionEnd(this._element);
			      }
			      this._clearInterval();
			    }
			    cycle() {
			      this._clearInterval();
			      this._updateInterval();
			      this._interval = setInterval(() => this.nextWhenVisible(), this._config.interval);
			    }
			    _maybeEnableCycle() {
			      if (!this._config.ride) {
			        return;
			      }
			      if (this._isSliding) {
			        EventHandler.one(this._element, EVENT_SLID, () => this.cycle());
			        return;
			      }
			      this.cycle();
			    }
			    to(index) {
			      const items = this._getItems();
			      if (index > items.length - 1 || index < 0) {
			        return;
			      }
			      if (this._isSliding) {
			        EventHandler.one(this._element, EVENT_SLID, () => this.to(index));
			        return;
			      }
			      const activeIndex = this._getItemIndex(this._getActive());
			      if (activeIndex === index) {
			        return;
			      }
			      const order = index > activeIndex ? ORDER_NEXT : ORDER_PREV;
			      this._slide(order, items[index]);
			    }
			    dispose() {
			      if (this._swipeHelper) {
			        this._swipeHelper.dispose();
			      }
			      super.dispose();
			    }

			    // Private
			    _configAfterMerge(config) {
			      config.defaultInterval = config.interval;
			      return config;
			    }
			    _addEventListeners() {
			      if (this._config.keyboard) {
			        EventHandler.on(this._element, EVENT_KEYDOWN, event => this._keydown(event));
			      }
			      if (this._config.pause === 'hover') {
			        EventHandler.on(this._element, EVENT_MOUSEENTER, () => this.pause());
			        EventHandler.on(this._element, EVENT_MOUSELEAVE, () => this._maybeEnableCycle());
			      }
			      if (this._config.touch && Swipe.isSupported()) {
			        this._addTouchEventListeners();
			      }
			    }
			    _addTouchEventListeners() {
			      for (const img of SelectorEngine.find(SELECTOR_ITEM_IMG, this._element)) {
			        EventHandler.on(img, EVENT_DRAG_START, event => event.preventDefault());
			      }
			      const endCallBack = () => {
			        if (this._config.pause !== 'hover') {
			          return;
			        }

			        // If it's a touch-enabled device, mouseenter/leave are fired as
			        // part of the mouse compatibility events on first tap - the carousel
			        // would stop cycling until user tapped out of it;
			        // here, we listen for touchend, explicitly pause the carousel
			        // (as if it's the second time we tap on it, mouseenter compat event
			        // is NOT fired) and after a timeout (to allow for mouse compatibility
			        // events to fire) we explicitly restart cycling

			        this.pause();
			        if (this.touchTimeout) {
			          clearTimeout(this.touchTimeout);
			        }
			        this.touchTimeout = setTimeout(() => this._maybeEnableCycle(), TOUCHEVENT_COMPAT_WAIT + this._config.interval);
			      };
			      const swipeConfig = {
			        leftCallback: () => this._slide(this._directionToOrder(DIRECTION_LEFT)),
			        rightCallback: () => this._slide(this._directionToOrder(DIRECTION_RIGHT)),
			        endCallback: endCallBack
			      };
			      this._swipeHelper = new Swipe(this._element, swipeConfig);
			    }
			    _keydown(event) {
			      if (/input|textarea/i.test(event.target.tagName)) {
			        return;
			      }
			      const direction = KEY_TO_DIRECTION[event.key];
			      if (direction) {
			        event.preventDefault();
			        this._slide(this._directionToOrder(direction));
			      }
			    }
			    _getItemIndex(element) {
			      return this._getItems().indexOf(element);
			    }
			    _setActiveIndicatorElement(index) {
			      if (!this._indicatorsElement) {
			        return;
			      }
			      const activeIndicator = SelectorEngine.findOne(SELECTOR_ACTIVE, this._indicatorsElement);
			      activeIndicator.classList.remove(CLASS_NAME_ACTIVE);
			      activeIndicator.removeAttribute('aria-current');
			      const newActiveIndicator = SelectorEngine.findOne(`[data-bs-slide-to="${index}"]`, this._indicatorsElement);
			      if (newActiveIndicator) {
			        newActiveIndicator.classList.add(CLASS_NAME_ACTIVE);
			        newActiveIndicator.setAttribute('aria-current', 'true');
			      }
			    }
			    _updateInterval() {
			      const element = this._activeElement || this._getActive();
			      if (!element) {
			        return;
			      }
			      const elementInterval = Number.parseInt(element.getAttribute('data-bs-interval'), 10);
			      this._config.interval = elementInterval || this._config.defaultInterval;
			    }
			    _slide(order, element = null) {
			      if (this._isSliding) {
			        return;
			      }
			      const activeElement = this._getActive();
			      const isNext = order === ORDER_NEXT;
			      const nextElement = element || index_js.getNextActiveElement(this._getItems(), activeElement, isNext, this._config.wrap);
			      if (nextElement === activeElement) {
			        return;
			      }
			      const nextElementIndex = this._getItemIndex(nextElement);
			      const triggerEvent = eventName => {
			        return EventHandler.trigger(this._element, eventName, {
			          relatedTarget: nextElement,
			          direction: this._orderToDirection(order),
			          from: this._getItemIndex(activeElement),
			          to: nextElementIndex
			        });
			      };
			      const slideEvent = triggerEvent(EVENT_SLIDE);
			      if (slideEvent.defaultPrevented) {
			        return;
			      }
			      if (!activeElement || !nextElement) {
			        // Some weirdness is happening, so we bail
			        // TODO: change tests that use empty divs to avoid this check
			        return;
			      }
			      const isCycling = Boolean(this._interval);
			      this.pause();
			      this._isSliding = true;
			      this._setActiveIndicatorElement(nextElementIndex);
			      this._activeElement = nextElement;
			      const directionalClassName = isNext ? CLASS_NAME_START : CLASS_NAME_END;
			      const orderClassName = isNext ? CLASS_NAME_NEXT : CLASS_NAME_PREV;
			      nextElement.classList.add(orderClassName);
			      index_js.reflow(nextElement);
			      activeElement.classList.add(directionalClassName);
			      nextElement.classList.add(directionalClassName);
			      const completeCallBack = () => {
			        nextElement.classList.remove(directionalClassName, orderClassName);
			        nextElement.classList.add(CLASS_NAME_ACTIVE);
			        activeElement.classList.remove(CLASS_NAME_ACTIVE, orderClassName, directionalClassName);
			        this._isSliding = false;
			        triggerEvent(EVENT_SLID);
			      };
			      this._queueCallback(completeCallBack, activeElement, this._isAnimated());
			      if (isCycling) {
			        this.cycle();
			      }
			    }
			    _isAnimated() {
			      return this._element.classList.contains(CLASS_NAME_SLIDE);
			    }
			    _getActive() {
			      return SelectorEngine.findOne(SELECTOR_ACTIVE_ITEM, this._element);
			    }
			    _getItems() {
			      return SelectorEngine.find(SELECTOR_ITEM, this._element);
			    }
			    _clearInterval() {
			      if (this._interval) {
			        clearInterval(this._interval);
			        this._interval = null;
			      }
			    }
			    _directionToOrder(direction) {
			      if (index_js.isRTL()) {
			        return direction === DIRECTION_LEFT ? ORDER_PREV : ORDER_NEXT;
			      }
			      return direction === DIRECTION_LEFT ? ORDER_NEXT : ORDER_PREV;
			    }
			    _orderToDirection(order) {
			      if (index_js.isRTL()) {
			        return order === ORDER_PREV ? DIRECTION_LEFT : DIRECTION_RIGHT;
			      }
			      return order === ORDER_PREV ? DIRECTION_RIGHT : DIRECTION_LEFT;
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Carousel.getOrCreateInstance(this, config);
			        if (typeof config === 'number') {
			          data.to(config);
			          return;
			        }
			        if (typeof config === 'string') {
			          if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
			            throw new TypeError(`No method named "${config}"`);
			          }
			          data[config]();
			        }
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_SLIDE, function (event) {
			    const target = SelectorEngine.getElementFromSelector(this);
			    if (!target || !target.classList.contains(CLASS_NAME_CAROUSEL)) {
			      return;
			    }
			    event.preventDefault();
			    const carousel = Carousel.getOrCreateInstance(target);
			    const slideIndex = this.getAttribute('data-bs-slide-to');
			    if (slideIndex) {
			      carousel.to(slideIndex);
			      carousel._maybeEnableCycle();
			      return;
			    }
			    if (Manipulator.getDataAttribute(this, 'slide') === 'next') {
			      carousel.next();
			      carousel._maybeEnableCycle();
			      return;
			    }
			    carousel.prev();
			    carousel._maybeEnableCycle();
			  });
			  EventHandler.on(window, EVENT_LOAD_DATA_API, () => {
			    const carousels = SelectorEngine.find(SELECTOR_DATA_RIDE);
			    for (const carousel of carousels) {
			      Carousel.getOrCreateInstance(carousel);
			    }
			  });

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Carousel);
			  return Carousel;
			}); 
		} (carousel$2));
		return carousel$2.exports;
	}

	var carouselExports = requireCarousel();
	var carousel = /*@__PURE__*/getDefaultExportFromCjs(carouselExports);

	var collapse$1 = {exports: {}};

	/*!
	  * Bootstrap collapse.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var collapse = collapse$1.exports;

	var hasRequiredCollapse;

	function requireCollapse () {
		if (hasRequiredCollapse) return collapse$1.exports;
		hasRequiredCollapse = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireSelectorEngine(), requireUtil()) ;
			})(collapse, function (BaseComponent, EventHandler, SelectorEngine, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap collapse.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'collapse';
			  const DATA_KEY = 'bs.collapse';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_NAME_COLLAPSE = 'collapse';
			  const CLASS_NAME_COLLAPSING = 'collapsing';
			  const CLASS_NAME_COLLAPSED = 'collapsed';
			  const CLASS_NAME_DEEPER_CHILDREN = `:scope .${CLASS_NAME_COLLAPSE} .${CLASS_NAME_COLLAPSE}`;
			  const CLASS_NAME_HORIZONTAL = 'collapse-horizontal';
			  const WIDTH = 'width';
			  const HEIGHT = 'height';
			  const SELECTOR_ACTIVES = '.collapse.show, .collapse.collapsing';
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="collapse"]';
			  const Default = {
			    parent: null,
			    toggle: true
			  };
			  const DefaultType = {
			    parent: '(null|element)',
			    toggle: 'boolean'
			  };

			  /**
			   * Class definition
			   */

			  class Collapse extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._isTransitioning = false;
			      this._triggerArray = [];
			      const toggleList = SelectorEngine.find(SELECTOR_DATA_TOGGLE);
			      for (const elem of toggleList) {
			        const selector = SelectorEngine.getSelectorFromElement(elem);
			        const filterElement = SelectorEngine.find(selector).filter(foundElement => foundElement === this._element);
			        if (selector !== null && filterElement.length) {
			          this._triggerArray.push(elem);
			        }
			      }
			      this._initializeChildren();
			      if (!this._config.parent) {
			        this._addAriaAndCollapsedClass(this._triggerArray, this._isShown());
			      }
			      if (this._config.toggle) {
			        this.toggle();
			      }
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    toggle() {
			      if (this._isShown()) {
			        this.hide();
			      } else {
			        this.show();
			      }
			    }
			    show() {
			      if (this._isTransitioning || this._isShown()) {
			        return;
			      }
			      let activeChildren = [];

			      // find active children
			      if (this._config.parent) {
			        activeChildren = this._getFirstLevelChildren(SELECTOR_ACTIVES).filter(element => element !== this._element).map(element => Collapse.getOrCreateInstance(element, {
			          toggle: false
			        }));
			      }
			      if (activeChildren.length && activeChildren[0]._isTransitioning) {
			        return;
			      }
			      const startEvent = EventHandler.trigger(this._element, EVENT_SHOW);
			      if (startEvent.defaultPrevented) {
			        return;
			      }
			      for (const activeInstance of activeChildren) {
			        activeInstance.hide();
			      }
			      const dimension = this._getDimension();
			      this._element.classList.remove(CLASS_NAME_COLLAPSE);
			      this._element.classList.add(CLASS_NAME_COLLAPSING);
			      this._element.style[dimension] = 0;
			      this._addAriaAndCollapsedClass(this._triggerArray, true);
			      this._isTransitioning = true;
			      const complete = () => {
			        this._isTransitioning = false;
			        this._element.classList.remove(CLASS_NAME_COLLAPSING);
			        this._element.classList.add(CLASS_NAME_COLLAPSE, CLASS_NAME_SHOW);
			        this._element.style[dimension] = '';
			        EventHandler.trigger(this._element, EVENT_SHOWN);
			      };
			      const capitalizedDimension = dimension[0].toUpperCase() + dimension.slice(1);
			      const scrollSize = `scroll${capitalizedDimension}`;
			      this._queueCallback(complete, this._element, true);
			      this._element.style[dimension] = `${this._element[scrollSize]}px`;
			    }
			    hide() {
			      if (this._isTransitioning || !this._isShown()) {
			        return;
			      }
			      const startEvent = EventHandler.trigger(this._element, EVENT_HIDE);
			      if (startEvent.defaultPrevented) {
			        return;
			      }
			      const dimension = this._getDimension();
			      this._element.style[dimension] = `${this._element.getBoundingClientRect()[dimension]}px`;
			      index_js.reflow(this._element);
			      this._element.classList.add(CLASS_NAME_COLLAPSING);
			      this._element.classList.remove(CLASS_NAME_COLLAPSE, CLASS_NAME_SHOW);
			      for (const trigger of this._triggerArray) {
			        const element = SelectorEngine.getElementFromSelector(trigger);
			        if (element && !this._isShown(element)) {
			          this._addAriaAndCollapsedClass([trigger], false);
			        }
			      }
			      this._isTransitioning = true;
			      const complete = () => {
			        this._isTransitioning = false;
			        this._element.classList.remove(CLASS_NAME_COLLAPSING);
			        this._element.classList.add(CLASS_NAME_COLLAPSE);
			        EventHandler.trigger(this._element, EVENT_HIDDEN);
			      };
			      this._element.style[dimension] = '';
			      this._queueCallback(complete, this._element, true);
			    }
			    _isShown(element = this._element) {
			      return element.classList.contains(CLASS_NAME_SHOW);
			    }

			    // Private
			    _configAfterMerge(config) {
			      config.toggle = Boolean(config.toggle); // Coerce string values
			      config.parent = index_js.getElement(config.parent);
			      return config;
			    }
			    _getDimension() {
			      return this._element.classList.contains(CLASS_NAME_HORIZONTAL) ? WIDTH : HEIGHT;
			    }
			    _initializeChildren() {
			      if (!this._config.parent) {
			        return;
			      }
			      const children = this._getFirstLevelChildren(SELECTOR_DATA_TOGGLE);
			      for (const element of children) {
			        const selected = SelectorEngine.getElementFromSelector(element);
			        if (selected) {
			          this._addAriaAndCollapsedClass([element], this._isShown(selected));
			        }
			      }
			    }
			    _getFirstLevelChildren(selector) {
			      const children = SelectorEngine.find(CLASS_NAME_DEEPER_CHILDREN, this._config.parent);
			      // remove children if greater depth
			      return SelectorEngine.find(selector, this._config.parent).filter(element => !children.includes(element));
			    }
			    _addAriaAndCollapsedClass(triggerArray, isOpen) {
			      if (!triggerArray.length) {
			        return;
			      }
			      for (const element of triggerArray) {
			        element.classList.toggle(CLASS_NAME_COLLAPSED, !isOpen);
			        element.setAttribute('aria-expanded', isOpen);
			      }
			    }

			    // Static
			    static jQueryInterface(config) {
			      const _config = {};
			      if (typeof config === 'string' && /show|hide/.test(config)) {
			        _config.toggle = false;
			      }
			      return this.each(function () {
			        const data = Collapse.getOrCreateInstance(this, _config);
			        if (typeof config === 'string') {
			          if (typeof data[config] === 'undefined') {
			            throw new TypeError(`No method named "${config}"`);
			          }
			          data[config]();
			        }
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
			    // preventDefault only for <a> elements (which change the URL) not inside the collapsible element
			    if (event.target.tagName === 'A' || event.delegateTarget && event.delegateTarget.tagName === 'A') {
			      event.preventDefault();
			    }
			    for (const element of SelectorEngine.getMultipleElementsFromSelector(this)) {
			      Collapse.getOrCreateInstance(element, {
			        toggle: false
			      }).toggle();
			    }
			  });

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Collapse);
			  return Collapse;
			}); 
		} (collapse$1));
		return collapse$1.exports;
	}

	var collapseExports = requireCollapse();
	var Collapse = /*@__PURE__*/getDefaultExportFromCjs(collapseExports);

	var dropdown$1 = {exports: {}};

	/*!
	  * Bootstrap dropdown.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var dropdown = dropdown$1.exports;

	var hasRequiredDropdown;

	function requireDropdown () {
		if (hasRequiredDropdown) return dropdown$1.exports;
		hasRequiredDropdown = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(require$$0, requireBaseComponent(), requireEventHandler(), requireManipulator(), requireSelectorEngine(), requireUtil()) ;
			})(dropdown, function (Popper, BaseComponent, EventHandler, Manipulator, SelectorEngine, index_js) {

			  function _interopNamespaceDefault(e) {
			    const n = Object.create(null, {
			      [Symbol.toStringTag]: {
			        value: 'Module'
			      }
			    });
			    if (e) {
			      for (const k in e) {
			        if (k !== 'default') {
			          const d = Object.getOwnPropertyDescriptor(e, k);
			          Object.defineProperty(n, k, d.get ? d : {
			            enumerable: true,
			            get: () => e[k]
			          });
			        }
			      }
			    }
			    n.default = e;
			    return Object.freeze(n);
			  }
			  const Popper__namespace = /*#__PURE__*/_interopNamespaceDefault(Popper);

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap dropdown.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */

			  const NAME = 'dropdown';
			  const DATA_KEY = 'bs.dropdown';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const ESCAPE_KEY = 'Escape';
			  const TAB_KEY = 'Tab';
			  const ARROW_UP_KEY = 'ArrowUp';
			  const ARROW_DOWN_KEY = 'ArrowDown';
			  const RIGHT_MOUSE_BUTTON = 2; // MouseEvent.button value for the secondary button, usually the right button

			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
			  const EVENT_KEYDOWN_DATA_API = `keydown${EVENT_KEY}${DATA_API_KEY}`;
			  const EVENT_KEYUP_DATA_API = `keyup${EVENT_KEY}${DATA_API_KEY}`;
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_NAME_DROPUP = 'dropup';
			  const CLASS_NAME_DROPEND = 'dropend';
			  const CLASS_NAME_DROPSTART = 'dropstart';
			  const CLASS_NAME_DROPUP_CENTER = 'dropup-center';
			  const CLASS_NAME_DROPDOWN_CENTER = 'dropdown-center';
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="dropdown"]:not(.disabled):not(:disabled)';
			  const SELECTOR_DATA_TOGGLE_SHOWN = `${SELECTOR_DATA_TOGGLE}.${CLASS_NAME_SHOW}`;
			  const SELECTOR_MENU = '.dropdown-menu';
			  const SELECTOR_NAVBAR = '.navbar';
			  const SELECTOR_NAVBAR_NAV = '.navbar-nav';
			  const SELECTOR_VISIBLE_ITEMS = '.dropdown-menu .dropdown-item:not(.disabled):not(:disabled)';
			  const PLACEMENT_TOP = index_js.isRTL() ? 'top-end' : 'top-start';
			  const PLACEMENT_TOPEND = index_js.isRTL() ? 'top-start' : 'top-end';
			  const PLACEMENT_BOTTOM = index_js.isRTL() ? 'bottom-end' : 'bottom-start';
			  const PLACEMENT_BOTTOMEND = index_js.isRTL() ? 'bottom-start' : 'bottom-end';
			  const PLACEMENT_RIGHT = index_js.isRTL() ? 'left-start' : 'right-start';
			  const PLACEMENT_LEFT = index_js.isRTL() ? 'right-start' : 'left-start';
			  const PLACEMENT_TOPCENTER = 'top';
			  const PLACEMENT_BOTTOMCENTER = 'bottom';
			  const Default = {
			    autoClose: true,
			    boundary: 'clippingParents',
			    display: 'dynamic',
			    offset: [0, 2],
			    popperConfig: null,
			    reference: 'toggle'
			  };
			  const DefaultType = {
			    autoClose: '(boolean|string)',
			    boundary: '(string|element)',
			    display: 'string',
			    offset: '(array|string|function)',
			    popperConfig: '(null|object|function)',
			    reference: '(string|element|object)'
			  };

			  /**
			   * Class definition
			   */

			  class Dropdown extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._popper = null;
			      this._parent = this._element.parentNode; // dropdown wrapper
			      // TODO: v6 revert #37011 & change markup https://getbootstrap.com/docs/5.3/forms/input-group/
			      this._menu = SelectorEngine.next(this._element, SELECTOR_MENU)[0] || SelectorEngine.prev(this._element, SELECTOR_MENU)[0] || SelectorEngine.findOne(SELECTOR_MENU, this._parent);
			      this._inNavbar = this._detectNavbar();
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    toggle() {
			      return this._isShown() ? this.hide() : this.show();
			    }
			    show() {
			      if (index_js.isDisabled(this._element) || this._isShown()) {
			        return;
			      }
			      const relatedTarget = {
			        relatedTarget: this._element
			      };
			      const showEvent = EventHandler.trigger(this._element, EVENT_SHOW, relatedTarget);
			      if (showEvent.defaultPrevented) {
			        return;
			      }
			      this._createPopper();

			      // If this is a touch-enabled device we add extra
			      // empty mouseover listeners to the body's immediate children;
			      // only needed because of broken event delegation on iOS
			      // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
			      if ('ontouchstart' in document.documentElement && !this._parent.closest(SELECTOR_NAVBAR_NAV)) {
			        for (const element of [].concat(...document.body.children)) {
			          EventHandler.on(element, 'mouseover', index_js.noop);
			        }
			      }
			      this._element.focus();
			      this._element.setAttribute('aria-expanded', true);
			      this._menu.classList.add(CLASS_NAME_SHOW);
			      this._element.classList.add(CLASS_NAME_SHOW);
			      EventHandler.trigger(this._element, EVENT_SHOWN, relatedTarget);
			    }
			    hide() {
			      if (index_js.isDisabled(this._element) || !this._isShown()) {
			        return;
			      }
			      const relatedTarget = {
			        relatedTarget: this._element
			      };
			      this._completeHide(relatedTarget);
			    }
			    dispose() {
			      if (this._popper) {
			        this._popper.destroy();
			      }
			      super.dispose();
			    }
			    update() {
			      this._inNavbar = this._detectNavbar();
			      if (this._popper) {
			        this._popper.update();
			      }
			    }

			    // Private
			    _completeHide(relatedTarget) {
			      const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE, relatedTarget);
			      if (hideEvent.defaultPrevented) {
			        return;
			      }

			      // If this is a touch-enabled device we remove the extra
			      // empty mouseover listeners we added for iOS support
			      if ('ontouchstart' in document.documentElement) {
			        for (const element of [].concat(...document.body.children)) {
			          EventHandler.off(element, 'mouseover', index_js.noop);
			        }
			      }
			      if (this._popper) {
			        this._popper.destroy();
			      }
			      this._menu.classList.remove(CLASS_NAME_SHOW);
			      this._element.classList.remove(CLASS_NAME_SHOW);
			      this._element.setAttribute('aria-expanded', 'false');
			      Manipulator.removeDataAttribute(this._menu, 'popper');
			      EventHandler.trigger(this._element, EVENT_HIDDEN, relatedTarget);
			    }
			    _getConfig(config) {
			      config = super._getConfig(config);
			      if (typeof config.reference === 'object' && !index_js.isElement(config.reference) && typeof config.reference.getBoundingClientRect !== 'function') {
			        // Popper virtual elements require a getBoundingClientRect method
			        throw new TypeError(`${NAME.toUpperCase()}: Option "reference" provided type "object" without a required "getBoundingClientRect" method.`);
			      }
			      return config;
			    }
			    _createPopper() {
			      if (typeof Popper__namespace === 'undefined') {
			        throw new TypeError('Bootstrap\'s dropdowns require Popper (https://popper.js.org)');
			      }
			      let referenceElement = this._element;
			      if (this._config.reference === 'parent') {
			        referenceElement = this._parent;
			      } else if (index_js.isElement(this._config.reference)) {
			        referenceElement = index_js.getElement(this._config.reference);
			      } else if (typeof this._config.reference === 'object') {
			        referenceElement = this._config.reference;
			      }
			      const popperConfig = this._getPopperConfig();
			      this._popper = Popper__namespace.createPopper(referenceElement, this._menu, popperConfig);
			    }
			    _isShown() {
			      return this._menu.classList.contains(CLASS_NAME_SHOW);
			    }
			    _getPlacement() {
			      const parentDropdown = this._parent;
			      if (parentDropdown.classList.contains(CLASS_NAME_DROPEND)) {
			        return PLACEMENT_RIGHT;
			      }
			      if (parentDropdown.classList.contains(CLASS_NAME_DROPSTART)) {
			        return PLACEMENT_LEFT;
			      }
			      if (parentDropdown.classList.contains(CLASS_NAME_DROPUP_CENTER)) {
			        return PLACEMENT_TOPCENTER;
			      }
			      if (parentDropdown.classList.contains(CLASS_NAME_DROPDOWN_CENTER)) {
			        return PLACEMENT_BOTTOMCENTER;
			      }

			      // We need to trim the value because custom properties can also include spaces
			      const isEnd = getComputedStyle(this._menu).getPropertyValue('--bs-position').trim() === 'end';
			      if (parentDropdown.classList.contains(CLASS_NAME_DROPUP)) {
			        return isEnd ? PLACEMENT_TOPEND : PLACEMENT_TOP;
			      }
			      return isEnd ? PLACEMENT_BOTTOMEND : PLACEMENT_BOTTOM;
			    }
			    _detectNavbar() {
			      return this._element.closest(SELECTOR_NAVBAR) !== null;
			    }
			    _getOffset() {
			      const {
			        offset
			      } = this._config;
			      if (typeof offset === 'string') {
			        return offset.split(',').map(value => Number.parseInt(value, 10));
			      }
			      if (typeof offset === 'function') {
			        return popperData => offset(popperData, this._element);
			      }
			      return offset;
			    }
			    _getPopperConfig() {
			      const defaultBsPopperConfig = {
			        placement: this._getPlacement(),
			        modifiers: [{
			          name: 'preventOverflow',
			          options: {
			            boundary: this._config.boundary
			          }
			        }, {
			          name: 'offset',
			          options: {
			            offset: this._getOffset()
			          }
			        }]
			      };

			      // Disable Popper if we have a static display or Dropdown is in Navbar
			      if (this._inNavbar || this._config.display === 'static') {
			        Manipulator.setDataAttribute(this._menu, 'popper', 'static'); // TODO: v6 remove
			        defaultBsPopperConfig.modifiers = [{
			          name: 'applyStyles',
			          enabled: false
			        }];
			      }
			      return {
			        ...defaultBsPopperConfig,
			        ...index_js.execute(this._config.popperConfig, [defaultBsPopperConfig])
			      };
			    }
			    _selectMenuItem({
			      key,
			      target
			    }) {
			      const items = SelectorEngine.find(SELECTOR_VISIBLE_ITEMS, this._menu).filter(element => index_js.isVisible(element));
			      if (!items.length) {
			        return;
			      }

			      // if target isn't included in items (e.g. when expanding the dropdown)
			      // allow cycling to get the last item in case key equals ARROW_UP_KEY
			      index_js.getNextActiveElement(items, target, key === ARROW_DOWN_KEY, !items.includes(target)).focus();
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Dropdown.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (typeof data[config] === 'undefined') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config]();
			      });
			    }
			    static clearMenus(event) {
			      if (event.button === RIGHT_MOUSE_BUTTON || event.type === 'keyup' && event.key !== TAB_KEY) {
			        return;
			      }
			      const openToggles = SelectorEngine.find(SELECTOR_DATA_TOGGLE_SHOWN);
			      for (const toggle of openToggles) {
			        const context = Dropdown.getInstance(toggle);
			        if (!context || context._config.autoClose === false) {
			          continue;
			        }
			        const composedPath = event.composedPath();
			        const isMenuTarget = composedPath.includes(context._menu);
			        if (composedPath.includes(context._element) || context._config.autoClose === 'inside' && !isMenuTarget || context._config.autoClose === 'outside' && isMenuTarget) {
			          continue;
			        }

			        // Tab navigation through the dropdown menu or events from contained inputs shouldn't close the menu
			        if (context._menu.contains(event.target) && (event.type === 'keyup' && event.key === TAB_KEY || /input|select|option|textarea|form/i.test(event.target.tagName))) {
			          continue;
			        }
			        const relatedTarget = {
			          relatedTarget: context._element
			        };
			        if (event.type === 'click') {
			          relatedTarget.clickEvent = event;
			        }
			        context._completeHide(relatedTarget);
			      }
			    }
			    static dataApiKeydownHandler(event) {
			      // If not an UP | DOWN | ESCAPE key => not a dropdown command
			      // If input/textarea && if key is other than ESCAPE => not a dropdown command

			      const isInput = /input|textarea/i.test(event.target.tagName);
			      const isEscapeEvent = event.key === ESCAPE_KEY;
			      const isUpOrDownEvent = [ARROW_UP_KEY, ARROW_DOWN_KEY].includes(event.key);
			      if (!isUpOrDownEvent && !isEscapeEvent) {
			        return;
			      }
			      if (isInput && !isEscapeEvent) {
			        return;
			      }
			      event.preventDefault();

			      // TODO: v6 revert #37011 & change markup https://getbootstrap.com/docs/5.3/forms/input-group/
			      const getToggleButton = this.matches(SELECTOR_DATA_TOGGLE) ? this : SelectorEngine.prev(this, SELECTOR_DATA_TOGGLE)[0] || SelectorEngine.next(this, SELECTOR_DATA_TOGGLE)[0] || SelectorEngine.findOne(SELECTOR_DATA_TOGGLE, event.delegateTarget.parentNode);
			      const instance = Dropdown.getOrCreateInstance(getToggleButton);
			      if (isUpOrDownEvent) {
			        event.stopPropagation();
			        instance.show();
			        instance._selectMenuItem(event);
			        return;
			      }
			      if (instance._isShown()) {
			        // else is escape and we check if it is shown
			        event.stopPropagation();
			        instance.hide();
			        getToggleButton.focus();
			      }
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_KEYDOWN_DATA_API, SELECTOR_DATA_TOGGLE, Dropdown.dataApiKeydownHandler);
			  EventHandler.on(document, EVENT_KEYDOWN_DATA_API, SELECTOR_MENU, Dropdown.dataApiKeydownHandler);
			  EventHandler.on(document, EVENT_CLICK_DATA_API, Dropdown.clearMenus);
			  EventHandler.on(document, EVENT_KEYUP_DATA_API, Dropdown.clearMenus);
			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
			    event.preventDefault();
			    Dropdown.getOrCreateInstance(this).toggle();
			  });

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Dropdown);
			  return Dropdown;
			}); 
		} (dropdown$1));
		return dropdown$1.exports;
	}

	var dropdownExports = requireDropdown();
	var Dropdown = /*@__PURE__*/getDefaultExportFromCjs(dropdownExports);

	var modal$1 = {exports: {}};

	var backdrop$1 = {exports: {}};

	/*!
	  * Bootstrap backdrop.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var backdrop = backdrop$1.exports;

	var hasRequiredBackdrop;

	function requireBackdrop () {
		if (hasRequiredBackdrop) return backdrop$1.exports;
		hasRequiredBackdrop = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireEventHandler(), requireConfig(), requireUtil()) ;
			})(backdrop, function (EventHandler, Config, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/backdrop.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'backdrop';
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_SHOW = 'show';
			  const EVENT_MOUSEDOWN = `mousedown.bs.${NAME}`;
			  const Default = {
			    className: 'modal-backdrop',
			    clickCallback: null,
			    isAnimated: false,
			    isVisible: true,
			    // if false, we use the backdrop helper without adding any element to the dom
			    rootElement: 'body' // give the choice to place backdrop under different elements
			  };
			  const DefaultType = {
			    className: 'string',
			    clickCallback: '(function|null)',
			    isAnimated: 'boolean',
			    isVisible: 'boolean',
			    rootElement: '(element|string)'
			  };

			  /**
			   * Class definition
			   */

			  class Backdrop extends Config {
			    constructor(config) {
			      super();
			      this._config = this._getConfig(config);
			      this._isAppended = false;
			      this._element = null;
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    show(callback) {
			      if (!this._config.isVisible) {
			        index_js.execute(callback);
			        return;
			      }
			      this._append();
			      const element = this._getElement();
			      if (this._config.isAnimated) {
			        index_js.reflow(element);
			      }
			      element.classList.add(CLASS_NAME_SHOW);
			      this._emulateAnimation(() => {
			        index_js.execute(callback);
			      });
			    }
			    hide(callback) {
			      if (!this._config.isVisible) {
			        index_js.execute(callback);
			        return;
			      }
			      this._getElement().classList.remove(CLASS_NAME_SHOW);
			      this._emulateAnimation(() => {
			        this.dispose();
			        index_js.execute(callback);
			      });
			    }
			    dispose() {
			      if (!this._isAppended) {
			        return;
			      }
			      EventHandler.off(this._element, EVENT_MOUSEDOWN);
			      this._element.remove();
			      this._isAppended = false;
			    }

			    // Private
			    _getElement() {
			      if (!this._element) {
			        const backdrop = document.createElement('div');
			        backdrop.className = this._config.className;
			        if (this._config.isAnimated) {
			          backdrop.classList.add(CLASS_NAME_FADE);
			        }
			        this._element = backdrop;
			      }
			      return this._element;
			    }
			    _configAfterMerge(config) {
			      // use getElement() with the default "body" to get a fresh Element on each instantiation
			      config.rootElement = index_js.getElement(config.rootElement);
			      return config;
			    }
			    _append() {
			      if (this._isAppended) {
			        return;
			      }
			      const element = this._getElement();
			      this._config.rootElement.append(element);
			      EventHandler.on(element, EVENT_MOUSEDOWN, () => {
			        index_js.execute(this._config.clickCallback);
			      });
			      this._isAppended = true;
			    }
			    _emulateAnimation(callback) {
			      index_js.executeAfterTransition(callback, this._getElement(), this._config.isAnimated);
			    }
			  }
			  return Backdrop;
			}); 
		} (backdrop$1));
		return backdrop$1.exports;
	}

	var focustrap$1 = {exports: {}};

	/*!
	  * Bootstrap focustrap.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var focustrap = focustrap$1.exports;

	var hasRequiredFocustrap;

	function requireFocustrap () {
		if (hasRequiredFocustrap) return focustrap$1.exports;
		hasRequiredFocustrap = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireEventHandler(), requireSelectorEngine(), requireConfig()) ;
			})(focustrap, function (EventHandler, SelectorEngine, Config) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/focustrap.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'focustrap';
			  const DATA_KEY = 'bs.focustrap';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const EVENT_FOCUSIN = `focusin${EVENT_KEY}`;
			  const EVENT_KEYDOWN_TAB = `keydown.tab${EVENT_KEY}`;
			  const TAB_KEY = 'Tab';
			  const TAB_NAV_FORWARD = 'forward';
			  const TAB_NAV_BACKWARD = 'backward';
			  const Default = {
			    autofocus: true,
			    trapElement: null // The element to trap focus inside of
			  };
			  const DefaultType = {
			    autofocus: 'boolean',
			    trapElement: 'element'
			  };

			  /**
			   * Class definition
			   */

			  class FocusTrap extends Config {
			    constructor(config) {
			      super();
			      this._config = this._getConfig(config);
			      this._isActive = false;
			      this._lastTabNavDirection = null;
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    activate() {
			      if (this._isActive) {
			        return;
			      }
			      if (this._config.autofocus) {
			        this._config.trapElement.focus();
			      }
			      EventHandler.off(document, EVENT_KEY); // guard against infinite focus loop
			      EventHandler.on(document, EVENT_FOCUSIN, event => this._handleFocusin(event));
			      EventHandler.on(document, EVENT_KEYDOWN_TAB, event => this._handleKeydown(event));
			      this._isActive = true;
			    }
			    deactivate() {
			      if (!this._isActive) {
			        return;
			      }
			      this._isActive = false;
			      EventHandler.off(document, EVENT_KEY);
			    }

			    // Private
			    _handleFocusin(event) {
			      const {
			        trapElement
			      } = this._config;
			      if (event.target === document || event.target === trapElement || trapElement.contains(event.target)) {
			        return;
			      }
			      const elements = SelectorEngine.focusableChildren(trapElement);
			      if (elements.length === 0) {
			        trapElement.focus();
			      } else if (this._lastTabNavDirection === TAB_NAV_BACKWARD) {
			        elements[elements.length - 1].focus();
			      } else {
			        elements[0].focus();
			      }
			    }
			    _handleKeydown(event) {
			      if (event.key !== TAB_KEY) {
			        return;
			      }
			      this._lastTabNavDirection = event.shiftKey ? TAB_NAV_BACKWARD : TAB_NAV_FORWARD;
			    }
			  }
			  return FocusTrap;
			}); 
		} (focustrap$1));
		return focustrap$1.exports;
	}

	var scrollbar$1 = {exports: {}};

	/*!
	  * Bootstrap scrollbar.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var scrollbar = scrollbar$1.exports;

	var hasRequiredScrollbar;

	function requireScrollbar () {
		if (hasRequiredScrollbar) return scrollbar$1.exports;
		hasRequiredScrollbar = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireManipulator(), requireSelectorEngine(), requireUtil()) ;
			})(scrollbar, function (Manipulator, SelectorEngine, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/scrollBar.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const SELECTOR_FIXED_CONTENT = '.fixed-top, .fixed-bottom, .is-fixed, .sticky-top';
			  const SELECTOR_STICKY_CONTENT = '.sticky-top';
			  const PROPERTY_PADDING = 'padding-right';
			  const PROPERTY_MARGIN = 'margin-right';

			  /**
			   * Class definition
			   */

			  class ScrollBarHelper {
			    constructor() {
			      this._element = document.body;
			    }

			    // Public
			    getWidth() {
			      // https://developer.mozilla.org/en-US/docs/Web/API/Window/innerWidth#usage_notes
			      const documentWidth = document.documentElement.clientWidth;
			      return Math.abs(window.innerWidth - documentWidth);
			    }
			    hide() {
			      const width = this.getWidth();
			      this._disableOverFlow();
			      // give padding to element to balance the hidden scrollbar width
			      this._setElementAttributes(this._element, PROPERTY_PADDING, calculatedValue => calculatedValue + width);
			      // trick: We adjust positive paddingRight and negative marginRight to sticky-top elements to keep showing fullwidth
			      this._setElementAttributes(SELECTOR_FIXED_CONTENT, PROPERTY_PADDING, calculatedValue => calculatedValue + width);
			      this._setElementAttributes(SELECTOR_STICKY_CONTENT, PROPERTY_MARGIN, calculatedValue => calculatedValue - width);
			    }
			    reset() {
			      this._resetElementAttributes(this._element, 'overflow');
			      this._resetElementAttributes(this._element, PROPERTY_PADDING);
			      this._resetElementAttributes(SELECTOR_FIXED_CONTENT, PROPERTY_PADDING);
			      this._resetElementAttributes(SELECTOR_STICKY_CONTENT, PROPERTY_MARGIN);
			    }
			    isOverflowing() {
			      return this.getWidth() > 0;
			    }

			    // Private
			    _disableOverFlow() {
			      this._saveInitialAttribute(this._element, 'overflow');
			      this._element.style.overflow = 'hidden';
			    }
			    _setElementAttributes(selector, styleProperty, callback) {
			      const scrollbarWidth = this.getWidth();
			      const manipulationCallBack = element => {
			        if (element !== this._element && window.innerWidth > element.clientWidth + scrollbarWidth) {
			          return;
			        }
			        this._saveInitialAttribute(element, styleProperty);
			        const calculatedValue = window.getComputedStyle(element).getPropertyValue(styleProperty);
			        element.style.setProperty(styleProperty, `${callback(Number.parseFloat(calculatedValue))}px`);
			      };
			      this._applyManipulationCallback(selector, manipulationCallBack);
			    }
			    _saveInitialAttribute(element, styleProperty) {
			      const actualValue = element.style.getPropertyValue(styleProperty);
			      if (actualValue) {
			        Manipulator.setDataAttribute(element, styleProperty, actualValue);
			      }
			    }
			    _resetElementAttributes(selector, styleProperty) {
			      const manipulationCallBack = element => {
			        const value = Manipulator.getDataAttribute(element, styleProperty);
			        // We only want to remove the property if the value is `null`; the value can also be zero
			        if (value === null) {
			          element.style.removeProperty(styleProperty);
			          return;
			        }
			        Manipulator.removeDataAttribute(element, styleProperty);
			        element.style.setProperty(styleProperty, value);
			      };
			      this._applyManipulationCallback(selector, manipulationCallBack);
			    }
			    _applyManipulationCallback(selector, callBack) {
			      if (index_js.isElement(selector)) {
			        callBack(selector);
			        return;
			      }
			      for (const sel of SelectorEngine.find(selector, this._element)) {
			        callBack(sel);
			      }
			    }
			  }
			  return ScrollBarHelper;
			}); 
		} (scrollbar$1));
		return scrollbar$1.exports;
	}

	/*!
	  * Bootstrap modal.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var modal = modal$1.exports;

	var hasRequiredModal;

	function requireModal () {
		if (hasRequiredModal) return modal$1.exports;
		hasRequiredModal = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireSelectorEngine(), requireBackdrop(), requireComponentFunctions(), requireFocustrap(), requireUtil(), requireScrollbar()) ;
			})(modal, function (BaseComponent, EventHandler, SelectorEngine, Backdrop, componentFunctions_js, FocusTrap, index_js, ScrollBarHelper) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap modal.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'modal';
			  const DATA_KEY = 'bs.modal';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const ESCAPE_KEY = 'Escape';
			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDE_PREVENTED = `hidePrevented${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const EVENT_RESIZE = `resize${EVENT_KEY}`;
			  const EVENT_CLICK_DISMISS = `click.dismiss${EVENT_KEY}`;
			  const EVENT_MOUSEDOWN_DISMISS = `mousedown.dismiss${EVENT_KEY}`;
			  const EVENT_KEYDOWN_DISMISS = `keydown.dismiss${EVENT_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
			  const CLASS_NAME_OPEN = 'modal-open';
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_NAME_STATIC = 'modal-static';
			  const OPEN_SELECTOR = '.modal.show';
			  const SELECTOR_DIALOG = '.modal-dialog';
			  const SELECTOR_MODAL_BODY = '.modal-body';
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="modal"]';
			  const Default = {
			    backdrop: true,
			    focus: true,
			    keyboard: true
			  };
			  const DefaultType = {
			    backdrop: '(boolean|string)',
			    focus: 'boolean',
			    keyboard: 'boolean'
			  };

			  /**
			   * Class definition
			   */

			  class Modal extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._dialog = SelectorEngine.findOne(SELECTOR_DIALOG, this._element);
			      this._backdrop = this._initializeBackDrop();
			      this._focustrap = this._initializeFocusTrap();
			      this._isShown = false;
			      this._isTransitioning = false;
			      this._scrollBar = new ScrollBarHelper();
			      this._addEventListeners();
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    toggle(relatedTarget) {
			      return this._isShown ? this.hide() : this.show(relatedTarget);
			    }
			    show(relatedTarget) {
			      if (this._isShown || this._isTransitioning) {
			        return;
			      }
			      const showEvent = EventHandler.trigger(this._element, EVENT_SHOW, {
			        relatedTarget
			      });
			      if (showEvent.defaultPrevented) {
			        return;
			      }
			      this._isShown = true;
			      this._isTransitioning = true;
			      this._scrollBar.hide();
			      document.body.classList.add(CLASS_NAME_OPEN);
			      this._adjustDialog();
			      this._backdrop.show(() => this._showElement(relatedTarget));
			    }
			    hide() {
			      if (!this._isShown || this._isTransitioning) {
			        return;
			      }
			      const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE);
			      if (hideEvent.defaultPrevented) {
			        return;
			      }
			      this._isShown = false;
			      this._isTransitioning = true;
			      this._focustrap.deactivate();
			      this._element.classList.remove(CLASS_NAME_SHOW);
			      this._queueCallback(() => this._hideModal(), this._element, this._isAnimated());
			    }
			    dispose() {
			      EventHandler.off(window, EVENT_KEY);
			      EventHandler.off(this._dialog, EVENT_KEY);
			      this._backdrop.dispose();
			      this._focustrap.deactivate();
			      super.dispose();
			    }
			    handleUpdate() {
			      this._adjustDialog();
			    }

			    // Private
			    _initializeBackDrop() {
			      return new Backdrop({
			        isVisible: Boolean(this._config.backdrop),
			        // 'static' option will be translated to true, and booleans will keep their value,
			        isAnimated: this._isAnimated()
			      });
			    }
			    _initializeFocusTrap() {
			      return new FocusTrap({
			        trapElement: this._element
			      });
			    }
			    _showElement(relatedTarget) {
			      // try to append dynamic modal
			      if (!document.body.contains(this._element)) {
			        document.body.append(this._element);
			      }
			      this._element.style.display = 'block';
			      this._element.removeAttribute('aria-hidden');
			      this._element.setAttribute('aria-modal', true);
			      this._element.setAttribute('role', 'dialog');
			      this._element.scrollTop = 0;
			      const modalBody = SelectorEngine.findOne(SELECTOR_MODAL_BODY, this._dialog);
			      if (modalBody) {
			        modalBody.scrollTop = 0;
			      }
			      index_js.reflow(this._element);
			      this._element.classList.add(CLASS_NAME_SHOW);
			      const transitionComplete = () => {
			        if (this._config.focus) {
			          this._focustrap.activate();
			        }
			        this._isTransitioning = false;
			        EventHandler.trigger(this._element, EVENT_SHOWN, {
			          relatedTarget
			        });
			      };
			      this._queueCallback(transitionComplete, this._dialog, this._isAnimated());
			    }
			    _addEventListeners() {
			      EventHandler.on(this._element, EVENT_KEYDOWN_DISMISS, event => {
			        if (event.key !== ESCAPE_KEY) {
			          return;
			        }
			        if (this._config.keyboard) {
			          this.hide();
			          return;
			        }
			        this._triggerBackdropTransition();
			      });
			      EventHandler.on(window, EVENT_RESIZE, () => {
			        if (this._isShown && !this._isTransitioning) {
			          this._adjustDialog();
			        }
			      });
			      EventHandler.on(this._element, EVENT_MOUSEDOWN_DISMISS, event => {
			        // a bad trick to segregate clicks that may start inside dialog but end outside, and avoid listen to scrollbar clicks
			        EventHandler.one(this._element, EVENT_CLICK_DISMISS, event2 => {
			          if (this._element !== event.target || this._element !== event2.target) {
			            return;
			          }
			          if (this._config.backdrop === 'static') {
			            this._triggerBackdropTransition();
			            return;
			          }
			          if (this._config.backdrop) {
			            this.hide();
			          }
			        });
			      });
			    }
			    _hideModal() {
			      this._element.style.display = 'none';
			      this._element.setAttribute('aria-hidden', true);
			      this._element.removeAttribute('aria-modal');
			      this._element.removeAttribute('role');
			      this._isTransitioning = false;
			      this._backdrop.hide(() => {
			        document.body.classList.remove(CLASS_NAME_OPEN);
			        this._resetAdjustments();
			        this._scrollBar.reset();
			        EventHandler.trigger(this._element, EVENT_HIDDEN);
			      });
			    }
			    _isAnimated() {
			      return this._element.classList.contains(CLASS_NAME_FADE);
			    }
			    _triggerBackdropTransition() {
			      const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE_PREVENTED);
			      if (hideEvent.defaultPrevented) {
			        return;
			      }
			      const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
			      const initialOverflowY = this._element.style.overflowY;
			      // return if the following background transition hasn't yet completed
			      if (initialOverflowY === 'hidden' || this._element.classList.contains(CLASS_NAME_STATIC)) {
			        return;
			      }
			      if (!isModalOverflowing) {
			        this._element.style.overflowY = 'hidden';
			      }
			      this._element.classList.add(CLASS_NAME_STATIC);
			      this._queueCallback(() => {
			        this._element.classList.remove(CLASS_NAME_STATIC);
			        this._queueCallback(() => {
			          this._element.style.overflowY = initialOverflowY;
			        }, this._dialog);
			      }, this._dialog);
			      this._element.focus();
			    }

			    /**
			     * The following methods are used to handle overflowing modals
			     */

			    _adjustDialog() {
			      const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
			      const scrollbarWidth = this._scrollBar.getWidth();
			      const isBodyOverflowing = scrollbarWidth > 0;
			      if (isBodyOverflowing && !isModalOverflowing) {
			        const property = index_js.isRTL() ? 'paddingLeft' : 'paddingRight';
			        this._element.style[property] = `${scrollbarWidth}px`;
			      }
			      if (!isBodyOverflowing && isModalOverflowing) {
			        const property = index_js.isRTL() ? 'paddingRight' : 'paddingLeft';
			        this._element.style[property] = `${scrollbarWidth}px`;
			      }
			    }
			    _resetAdjustments() {
			      this._element.style.paddingLeft = '';
			      this._element.style.paddingRight = '';
			    }

			    // Static
			    static jQueryInterface(config, relatedTarget) {
			      return this.each(function () {
			        const data = Modal.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (typeof data[config] === 'undefined') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config](relatedTarget);
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
			    const target = SelectorEngine.getElementFromSelector(this);
			    if (['A', 'AREA'].includes(this.tagName)) {
			      event.preventDefault();
			    }
			    EventHandler.one(target, EVENT_SHOW, showEvent => {
			      if (showEvent.defaultPrevented) {
			        // only register focus restorer if modal will actually get shown
			        return;
			      }
			      EventHandler.one(target, EVENT_HIDDEN, () => {
			        if (index_js.isVisible(this)) {
			          this.focus();
			        }
			      });
			    });

			    // avoid conflict when clicking modal toggler while another one is open
			    const alreadyOpen = SelectorEngine.findOne(OPEN_SELECTOR);
			    if (alreadyOpen) {
			      Modal.getInstance(alreadyOpen).hide();
			    }
			    const data = Modal.getOrCreateInstance(target);
			    data.toggle(this);
			  });
			  componentFunctions_js.enableDismissTrigger(Modal);

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Modal);
			  return Modal;
			}); 
		} (modal$1));
		return modal$1.exports;
	}

	var modalExports = requireModal();
	var Modal = /*@__PURE__*/getDefaultExportFromCjs(modalExports);

	var offcanvas$1 = {exports: {}};

	/*!
	  * Bootstrap offcanvas.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var offcanvas = offcanvas$1.exports;

	var hasRequiredOffcanvas;

	function requireOffcanvas () {
		if (hasRequiredOffcanvas) return offcanvas$1.exports;
		hasRequiredOffcanvas = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireSelectorEngine(), requireBackdrop(), requireComponentFunctions(), requireFocustrap(), requireUtil(), requireScrollbar()) ;
			})(offcanvas, function (BaseComponent, EventHandler, SelectorEngine, Backdrop, componentFunctions_js, FocusTrap, index_js, ScrollBarHelper) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap offcanvas.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'offcanvas';
			  const DATA_KEY = 'bs.offcanvas';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const EVENT_LOAD_DATA_API = `load${EVENT_KEY}${DATA_API_KEY}`;
			  const ESCAPE_KEY = 'Escape';
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_NAME_SHOWING = 'showing';
			  const CLASS_NAME_HIDING = 'hiding';
			  const CLASS_NAME_BACKDROP = 'offcanvas-backdrop';
			  const OPEN_SELECTOR = '.offcanvas.show';
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDE_PREVENTED = `hidePrevented${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_RESIZE = `resize${EVENT_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
			  const EVENT_KEYDOWN_DISMISS = `keydown.dismiss${EVENT_KEY}`;
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="offcanvas"]';
			  const Default = {
			    backdrop: true,
			    keyboard: true,
			    scroll: false
			  };
			  const DefaultType = {
			    backdrop: '(boolean|string)',
			    keyboard: 'boolean',
			    scroll: 'boolean'
			  };

			  /**
			   * Class definition
			   */

			  class Offcanvas extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._isShown = false;
			      this._backdrop = this._initializeBackDrop();
			      this._focustrap = this._initializeFocusTrap();
			      this._addEventListeners();
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    toggle(relatedTarget) {
			      return this._isShown ? this.hide() : this.show(relatedTarget);
			    }
			    show(relatedTarget) {
			      if (this._isShown) {
			        return;
			      }
			      const showEvent = EventHandler.trigger(this._element, EVENT_SHOW, {
			        relatedTarget
			      });
			      if (showEvent.defaultPrevented) {
			        return;
			      }
			      this._isShown = true;
			      this._backdrop.show();
			      if (!this._config.scroll) {
			        new ScrollBarHelper().hide();
			      }
			      this._element.setAttribute('aria-modal', true);
			      this._element.setAttribute('role', 'dialog');
			      this._element.classList.add(CLASS_NAME_SHOWING);
			      const completeCallBack = () => {
			        if (!this._config.scroll || this._config.backdrop) {
			          this._focustrap.activate();
			        }
			        this._element.classList.add(CLASS_NAME_SHOW);
			        this._element.classList.remove(CLASS_NAME_SHOWING);
			        EventHandler.trigger(this._element, EVENT_SHOWN, {
			          relatedTarget
			        });
			      };
			      this._queueCallback(completeCallBack, this._element, true);
			    }
			    hide() {
			      if (!this._isShown) {
			        return;
			      }
			      const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE);
			      if (hideEvent.defaultPrevented) {
			        return;
			      }
			      this._focustrap.deactivate();
			      this._element.blur();
			      this._isShown = false;
			      this._element.classList.add(CLASS_NAME_HIDING);
			      this._backdrop.hide();
			      const completeCallback = () => {
			        this._element.classList.remove(CLASS_NAME_SHOW, CLASS_NAME_HIDING);
			        this._element.removeAttribute('aria-modal');
			        this._element.removeAttribute('role');
			        if (!this._config.scroll) {
			          new ScrollBarHelper().reset();
			        }
			        EventHandler.trigger(this._element, EVENT_HIDDEN);
			      };
			      this._queueCallback(completeCallback, this._element, true);
			    }
			    dispose() {
			      this._backdrop.dispose();
			      this._focustrap.deactivate();
			      super.dispose();
			    }

			    // Private
			    _initializeBackDrop() {
			      const clickCallback = () => {
			        if (this._config.backdrop === 'static') {
			          EventHandler.trigger(this._element, EVENT_HIDE_PREVENTED);
			          return;
			        }
			        this.hide();
			      };

			      // 'static' option will be translated to true, and booleans will keep their value
			      const isVisible = Boolean(this._config.backdrop);
			      return new Backdrop({
			        className: CLASS_NAME_BACKDROP,
			        isVisible,
			        isAnimated: true,
			        rootElement: this._element.parentNode,
			        clickCallback: isVisible ? clickCallback : null
			      });
			    }
			    _initializeFocusTrap() {
			      return new FocusTrap({
			        trapElement: this._element
			      });
			    }
			    _addEventListeners() {
			      EventHandler.on(this._element, EVENT_KEYDOWN_DISMISS, event => {
			        if (event.key !== ESCAPE_KEY) {
			          return;
			        }
			        if (this._config.keyboard) {
			          this.hide();
			          return;
			        }
			        EventHandler.trigger(this._element, EVENT_HIDE_PREVENTED);
			      });
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Offcanvas.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config](this);
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
			    const target = SelectorEngine.getElementFromSelector(this);
			    if (['A', 'AREA'].includes(this.tagName)) {
			      event.preventDefault();
			    }
			    if (index_js.isDisabled(this)) {
			      return;
			    }
			    EventHandler.one(target, EVENT_HIDDEN, () => {
			      // focus on trigger when it is closed
			      if (index_js.isVisible(this)) {
			        this.focus();
			      }
			    });

			    // avoid conflict when clicking a toggler of an offcanvas, while another is open
			    const alreadyOpen = SelectorEngine.findOne(OPEN_SELECTOR);
			    if (alreadyOpen && alreadyOpen !== target) {
			      Offcanvas.getInstance(alreadyOpen).hide();
			    }
			    const data = Offcanvas.getOrCreateInstance(target);
			    data.toggle(this);
			  });
			  EventHandler.on(window, EVENT_LOAD_DATA_API, () => {
			    for (const selector of SelectorEngine.find(OPEN_SELECTOR)) {
			      Offcanvas.getOrCreateInstance(selector).show();
			    }
			  });
			  EventHandler.on(window, EVENT_RESIZE, () => {
			    for (const element of SelectorEngine.find('[aria-modal][class*=show][class*=offcanvas-]')) {
			      if (getComputedStyle(element).position !== 'fixed') {
			        Offcanvas.getOrCreateInstance(element).hide();
			      }
			    }
			  });
			  componentFunctions_js.enableDismissTrigger(Offcanvas);

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Offcanvas);
			  return Offcanvas;
			}); 
		} (offcanvas$1));
		return offcanvas$1.exports;
	}

	var offcanvasExports = requireOffcanvas();
	var Offcanvas = /*@__PURE__*/getDefaultExportFromCjs(offcanvasExports);

	var popover$2 = {exports: {}};

	var tooltip$2 = {exports: {}};

	var sanitizer$1 = {exports: {}};

	/*!
	  * Bootstrap sanitizer.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var sanitizer = sanitizer$1.exports;

	var hasRequiredSanitizer;

	function requireSanitizer () {
		if (hasRequiredSanitizer) return sanitizer$1.exports;
		hasRequiredSanitizer = 1;
		(function (module, exports) {
			(function (global, factory) {
			  factory(exports) ;
			})(sanitizer, function (exports) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/sanitizer.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  // js-docs-start allow-list
			  const ARIA_ATTRIBUTE_PATTERN = /^aria-[\w-]*$/i;
			  const DefaultAllowlist = {
			    // Global attributes allowed on any supplied element below.
			    '*': ['class', 'dir', 'id', 'lang', 'role', ARIA_ATTRIBUTE_PATTERN],
			    a: ['target', 'href', 'title', 'rel'],
			    area: [],
			    b: [],
			    br: [],
			    col: [],
			    code: [],
			    dd: [],
			    div: [],
			    dl: [],
			    dt: [],
			    em: [],
			    hr: [],
			    h1: [],
			    h2: [],
			    h3: [],
			    h4: [],
			    h5: [],
			    h6: [],
			    i: [],
			    img: ['src', 'srcset', 'alt', 'title', 'width', 'height'],
			    li: [],
			    ol: [],
			    p: [],
			    pre: [],
			    s: [],
			    small: [],
			    span: [],
			    sub: [],
			    sup: [],
			    strong: [],
			    u: [],
			    ul: []
			  };
			  // js-docs-end allow-list

			  const uriAttributes = new Set(['background', 'cite', 'href', 'itemtype', 'longdesc', 'poster', 'src', 'xlink:href']);

			  /**
			   * A pattern that recognizes URLs that are safe wrt. XSS in URL navigation
			   * contexts.
			   *
			   * Shout-out to Angular https://github.com/angular/angular/blob/15.2.8/packages/core/src/sanitization/url_sanitizer.ts#L38
			   */
			  // eslint-disable-next-line unicorn/better-regex
			  const SAFE_URL_PATTERN = /^(?!javascript:)(?:[a-z0-9+.-]+:|[^&:/?#]*(?:[/?#]|$))/i;
			  const allowedAttribute = (attribute, allowedAttributeList) => {
			    const attributeName = attribute.nodeName.toLowerCase();
			    if (allowedAttributeList.includes(attributeName)) {
			      if (uriAttributes.has(attributeName)) {
			        return Boolean(SAFE_URL_PATTERN.test(attribute.nodeValue));
			      }
			      return true;
			    }

			    // Check if a regular expression validates the attribute.
			    return allowedAttributeList.filter(attributeRegex => attributeRegex instanceof RegExp).some(regex => regex.test(attributeName));
			  };
			  function sanitizeHtml(unsafeHtml, allowList, sanitizeFunction) {
			    if (!unsafeHtml.length) {
			      return unsafeHtml;
			    }
			    if (sanitizeFunction && typeof sanitizeFunction === 'function') {
			      return sanitizeFunction(unsafeHtml);
			    }
			    const domParser = new window.DOMParser();
			    const createdDocument = domParser.parseFromString(unsafeHtml, 'text/html');
			    const elements = [].concat(...createdDocument.body.querySelectorAll('*'));
			    for (const element of elements) {
			      const elementName = element.nodeName.toLowerCase();
			      if (!Object.keys(allowList).includes(elementName)) {
			        element.remove();
			        continue;
			      }
			      const attributeList = [].concat(...element.attributes);
			      const allowedAttributes = [].concat(allowList['*'] || [], allowList[elementName] || []);
			      for (const attribute of attributeList) {
			        if (!allowedAttribute(attribute, allowedAttributes)) {
			          element.removeAttribute(attribute.nodeName);
			        }
			      }
			    }
			    return createdDocument.body.innerHTML;
			  }
			  exports.DefaultAllowlist = DefaultAllowlist;
			  exports.sanitizeHtml = sanitizeHtml;
			  Object.defineProperty(exports, Symbol.toStringTag, {
			    value: 'Module'
			  });
			}); 
		} (sanitizer$1, sanitizer$1.exports));
		return sanitizer$1.exports;
	}

	var templateFactory$1 = {exports: {}};

	/*!
	  * Bootstrap template-factory.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var templateFactory = templateFactory$1.exports;

	var hasRequiredTemplateFactory;

	function requireTemplateFactory () {
		if (hasRequiredTemplateFactory) return templateFactory$1.exports;
		hasRequiredTemplateFactory = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireSelectorEngine(), requireConfig(), requireSanitizer(), requireUtil()) ;
			})(templateFactory, function (SelectorEngine, Config, sanitizer_js, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap util/template-factory.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'TemplateFactory';
			  const Default = {
			    allowList: sanitizer_js.DefaultAllowlist,
			    content: {},
			    // { selector : text ,  selector2 : text2 , }
			    extraClass: '',
			    html: false,
			    sanitize: true,
			    sanitizeFn: null,
			    template: '<div></div>'
			  };
			  const DefaultType = {
			    allowList: 'object',
			    content: 'object',
			    extraClass: '(string|function)',
			    html: 'boolean',
			    sanitize: 'boolean',
			    sanitizeFn: '(null|function)',
			    template: 'string'
			  };
			  const DefaultContentType = {
			    entry: '(string|element|function|null)',
			    selector: '(string|element)'
			  };

			  /**
			   * Class definition
			   */

			  class TemplateFactory extends Config {
			    constructor(config) {
			      super();
			      this._config = this._getConfig(config);
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    getContent() {
			      return Object.values(this._config.content).map(config => this._resolvePossibleFunction(config)).filter(Boolean);
			    }
			    hasContent() {
			      return this.getContent().length > 0;
			    }
			    changeContent(content) {
			      this._checkContent(content);
			      this._config.content = {
			        ...this._config.content,
			        ...content
			      };
			      return this;
			    }
			    toHtml() {
			      const templateWrapper = document.createElement('div');
			      templateWrapper.innerHTML = this._maybeSanitize(this._config.template);
			      for (const [selector, text] of Object.entries(this._config.content)) {
			        this._setContent(templateWrapper, text, selector);
			      }
			      const template = templateWrapper.children[0];
			      const extraClass = this._resolvePossibleFunction(this._config.extraClass);
			      if (extraClass) {
			        template.classList.add(...extraClass.split(' '));
			      }
			      return template;
			    }

			    // Private
			    _typeCheckConfig(config) {
			      super._typeCheckConfig(config);
			      this._checkContent(config.content);
			    }
			    _checkContent(arg) {
			      for (const [selector, content] of Object.entries(arg)) {
			        super._typeCheckConfig({
			          selector,
			          entry: content
			        }, DefaultContentType);
			      }
			    }
			    _setContent(template, content, selector) {
			      const templateElement = SelectorEngine.findOne(selector, template);
			      if (!templateElement) {
			        return;
			      }
			      content = this._resolvePossibleFunction(content);
			      if (!content) {
			        templateElement.remove();
			        return;
			      }
			      if (index_js.isElement(content)) {
			        this._putElementInTemplate(index_js.getElement(content), templateElement);
			        return;
			      }
			      if (this._config.html) {
			        templateElement.innerHTML = this._maybeSanitize(content);
			        return;
			      }
			      templateElement.textContent = content;
			    }
			    _maybeSanitize(arg) {
			      return this._config.sanitize ? sanitizer_js.sanitizeHtml(arg, this._config.allowList, this._config.sanitizeFn) : arg;
			    }
			    _resolvePossibleFunction(arg) {
			      return index_js.execute(arg, [this]);
			    }
			    _putElementInTemplate(element, templateElement) {
			      if (this._config.html) {
			        templateElement.innerHTML = '';
			        templateElement.append(element);
			        return;
			      }
			      templateElement.textContent = element.textContent;
			    }
			  }
			  return TemplateFactory;
			}); 
		} (templateFactory$1));
		return templateFactory$1.exports;
	}

	/*!
	  * Bootstrap tooltip.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var tooltip$1 = tooltip$2.exports;

	var hasRequiredTooltip;

	function requireTooltip () {
		if (hasRequiredTooltip) return tooltip$2.exports;
		hasRequiredTooltip = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(require$$0, requireBaseComponent(), requireEventHandler(), requireManipulator(), requireUtil(), requireSanitizer(), requireTemplateFactory()) ;
			})(tooltip$1, function (Popper, BaseComponent, EventHandler, Manipulator, index_js, sanitizer_js, TemplateFactory) {

			  function _interopNamespaceDefault(e) {
			    const n = Object.create(null, {
			      [Symbol.toStringTag]: {
			        value: 'Module'
			      }
			    });
			    if (e) {
			      for (const k in e) {
			        if (k !== 'default') {
			          const d = Object.getOwnPropertyDescriptor(e, k);
			          Object.defineProperty(n, k, d.get ? d : {
			            enumerable: true,
			            get: () => e[k]
			          });
			        }
			      }
			    }
			    n.default = e;
			    return Object.freeze(n);
			  }
			  const Popper__namespace = /*#__PURE__*/_interopNamespaceDefault(Popper);

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap tooltip.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */

			  const NAME = 'tooltip';
			  const DISALLOWED_ATTRIBUTES = new Set(['sanitize', 'allowList', 'sanitizeFn']);
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_MODAL = 'modal';
			  const CLASS_NAME_SHOW = 'show';
			  const SELECTOR_TOOLTIP_INNER = '.tooltip-inner';
			  const SELECTOR_MODAL = `.${CLASS_NAME_MODAL}`;
			  const EVENT_MODAL_HIDE = 'hide.bs.modal';
			  const TRIGGER_HOVER = 'hover';
			  const TRIGGER_FOCUS = 'focus';
			  const TRIGGER_CLICK = 'click';
			  const TRIGGER_MANUAL = 'manual';
			  const EVENT_HIDE = 'hide';
			  const EVENT_HIDDEN = 'hidden';
			  const EVENT_SHOW = 'show';
			  const EVENT_SHOWN = 'shown';
			  const EVENT_INSERTED = 'inserted';
			  const EVENT_CLICK = 'click';
			  const EVENT_FOCUSIN = 'focusin';
			  const EVENT_FOCUSOUT = 'focusout';
			  const EVENT_MOUSEENTER = 'mouseenter';
			  const EVENT_MOUSELEAVE = 'mouseleave';
			  const AttachmentMap = {
			    AUTO: 'auto',
			    TOP: 'top',
			    RIGHT: index_js.isRTL() ? 'left' : 'right',
			    BOTTOM: 'bottom',
			    LEFT: index_js.isRTL() ? 'right' : 'left'
			  };
			  const Default = {
			    allowList: sanitizer_js.DefaultAllowlist,
			    animation: true,
			    boundary: 'clippingParents',
			    container: false,
			    customClass: '',
			    delay: 0,
			    fallbackPlacements: ['top', 'right', 'bottom', 'left'],
			    html: false,
			    offset: [0, 6],
			    placement: 'top',
			    popperConfig: null,
			    sanitize: true,
			    sanitizeFn: null,
			    selector: false,
			    template: '<div class="tooltip" role="tooltip">' + '<div class="tooltip-arrow"></div>' + '<div class="tooltip-inner"></div>' + '</div>',
			    title: '',
			    trigger: 'hover focus'
			  };
			  const DefaultType = {
			    allowList: 'object',
			    animation: 'boolean',
			    boundary: '(string|element)',
			    container: '(string|element|boolean)',
			    customClass: '(string|function)',
			    delay: '(number|object)',
			    fallbackPlacements: 'array',
			    html: 'boolean',
			    offset: '(array|string|function)',
			    placement: '(string|function)',
			    popperConfig: '(null|object|function)',
			    sanitize: 'boolean',
			    sanitizeFn: '(null|function)',
			    selector: '(string|boolean)',
			    template: 'string',
			    title: '(string|element|function)',
			    trigger: 'string'
			  };

			  /**
			   * Class definition
			   */

			  class Tooltip extends BaseComponent {
			    constructor(element, config) {
			      if (typeof Popper__namespace === 'undefined') {
			        throw new TypeError('Bootstrap\'s tooltips require Popper (https://popper.js.org)');
			      }
			      super(element, config);

			      // Private
			      this._isEnabled = true;
			      this._timeout = 0;
			      this._isHovered = null;
			      this._activeTrigger = {};
			      this._popper = null;
			      this._templateFactory = null;
			      this._newContent = null;

			      // Protected
			      this.tip = null;
			      this._setListeners();
			      if (!this._config.selector) {
			        this._fixTitle();
			      }
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    enable() {
			      this._isEnabled = true;
			    }
			    disable() {
			      this._isEnabled = false;
			    }
			    toggleEnabled() {
			      this._isEnabled = !this._isEnabled;
			    }
			    toggle() {
			      if (!this._isEnabled) {
			        return;
			      }
			      this._activeTrigger.click = !this._activeTrigger.click;
			      if (this._isShown()) {
			        this._leave();
			        return;
			      }
			      this._enter();
			    }
			    dispose() {
			      clearTimeout(this._timeout);
			      EventHandler.off(this._element.closest(SELECTOR_MODAL), EVENT_MODAL_HIDE, this._hideModalHandler);
			      if (this._element.getAttribute('data-bs-original-title')) {
			        this._element.setAttribute('title', this._element.getAttribute('data-bs-original-title'));
			      }
			      this._disposePopper();
			      super.dispose();
			    }
			    show() {
			      if (this._element.style.display === 'none') {
			        throw new Error('Please use show on visible elements');
			      }
			      if (!(this._isWithContent() && this._isEnabled)) {
			        return;
			      }
			      const showEvent = EventHandler.trigger(this._element, this.constructor.eventName(EVENT_SHOW));
			      const shadowRoot = index_js.findShadowRoot(this._element);
			      const isInTheDom = (shadowRoot || this._element.ownerDocument.documentElement).contains(this._element);
			      if (showEvent.defaultPrevented || !isInTheDom) {
			        return;
			      }

			      // TODO: v6 remove this or make it optional
			      this._disposePopper();
			      const tip = this._getTipElement();
			      this._element.setAttribute('aria-describedby', tip.getAttribute('id'));
			      const {
			        container
			      } = this._config;
			      if (!this._element.ownerDocument.documentElement.contains(this.tip)) {
			        container.append(tip);
			        EventHandler.trigger(this._element, this.constructor.eventName(EVENT_INSERTED));
			      }
			      this._popper = this._createPopper(tip);
			      tip.classList.add(CLASS_NAME_SHOW);

			      // If this is a touch-enabled device we add extra
			      // empty mouseover listeners to the body's immediate children;
			      // only needed because of broken event delegation on iOS
			      // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html
			      if ('ontouchstart' in document.documentElement) {
			        for (const element of [].concat(...document.body.children)) {
			          EventHandler.on(element, 'mouseover', index_js.noop);
			        }
			      }
			      const complete = () => {
			        EventHandler.trigger(this._element, this.constructor.eventName(EVENT_SHOWN));
			        if (this._isHovered === false) {
			          this._leave();
			        }
			        this._isHovered = false;
			      };
			      this._queueCallback(complete, this.tip, this._isAnimated());
			    }
			    hide() {
			      if (!this._isShown()) {
			        return;
			      }
			      const hideEvent = EventHandler.trigger(this._element, this.constructor.eventName(EVENT_HIDE));
			      if (hideEvent.defaultPrevented) {
			        return;
			      }
			      const tip = this._getTipElement();
			      tip.classList.remove(CLASS_NAME_SHOW);

			      // If this is a touch-enabled device we remove the extra
			      // empty mouseover listeners we added for iOS support
			      if ('ontouchstart' in document.documentElement) {
			        for (const element of [].concat(...document.body.children)) {
			          EventHandler.off(element, 'mouseover', index_js.noop);
			        }
			      }
			      this._activeTrigger[TRIGGER_CLICK] = false;
			      this._activeTrigger[TRIGGER_FOCUS] = false;
			      this._activeTrigger[TRIGGER_HOVER] = false;
			      this._isHovered = null; // it is a trick to support manual triggering

			      const complete = () => {
			        if (this._isWithActiveTrigger()) {
			          return;
			        }
			        if (!this._isHovered) {
			          this._disposePopper();
			        }
			        this._element.removeAttribute('aria-describedby');
			        EventHandler.trigger(this._element, this.constructor.eventName(EVENT_HIDDEN));
			      };
			      this._queueCallback(complete, this.tip, this._isAnimated());
			    }
			    update() {
			      if (this._popper) {
			        this._popper.update();
			      }
			    }

			    // Protected
			    _isWithContent() {
			      return Boolean(this._getTitle());
			    }
			    _getTipElement() {
			      if (!this.tip) {
			        this.tip = this._createTipElement(this._newContent || this._getContentForTemplate());
			      }
			      return this.tip;
			    }
			    _createTipElement(content) {
			      const tip = this._getTemplateFactory(content).toHtml();

			      // TODO: remove this check in v6
			      if (!tip) {
			        return null;
			      }
			      tip.classList.remove(CLASS_NAME_FADE, CLASS_NAME_SHOW);
			      // TODO: v6 the following can be achieved with CSS only
			      tip.classList.add(`bs-${this.constructor.NAME}-auto`);
			      const tipId = index_js.getUID(this.constructor.NAME).toString();
			      tip.setAttribute('id', tipId);
			      if (this._isAnimated()) {
			        tip.classList.add(CLASS_NAME_FADE);
			      }
			      return tip;
			    }
			    setContent(content) {
			      this._newContent = content;
			      if (this._isShown()) {
			        this._disposePopper();
			        this.show();
			      }
			    }
			    _getTemplateFactory(content) {
			      if (this._templateFactory) {
			        this._templateFactory.changeContent(content);
			      } else {
			        this._templateFactory = new TemplateFactory({
			          ...this._config,
			          // the `content` var has to be after `this._config`
			          // to override config.content in case of popover
			          content,
			          extraClass: this._resolvePossibleFunction(this._config.customClass)
			        });
			      }
			      return this._templateFactory;
			    }
			    _getContentForTemplate() {
			      return {
			        [SELECTOR_TOOLTIP_INNER]: this._getTitle()
			      };
			    }
			    _getTitle() {
			      return this._resolvePossibleFunction(this._config.title) || this._element.getAttribute('data-bs-original-title');
			    }

			    // Private
			    _initializeOnDelegatedTarget(event) {
			      return this.constructor.getOrCreateInstance(event.delegateTarget, this._getDelegateConfig());
			    }
			    _isAnimated() {
			      return this._config.animation || this.tip && this.tip.classList.contains(CLASS_NAME_FADE);
			    }
			    _isShown() {
			      return this.tip && this.tip.classList.contains(CLASS_NAME_SHOW);
			    }
			    _createPopper(tip) {
			      const placement = index_js.execute(this._config.placement, [this, tip, this._element]);
			      const attachment = AttachmentMap[placement.toUpperCase()];
			      return Popper__namespace.createPopper(this._element, tip, this._getPopperConfig(attachment));
			    }
			    _getOffset() {
			      const {
			        offset
			      } = this._config;
			      if (typeof offset === 'string') {
			        return offset.split(',').map(value => Number.parseInt(value, 10));
			      }
			      if (typeof offset === 'function') {
			        return popperData => offset(popperData, this._element);
			      }
			      return offset;
			    }
			    _resolvePossibleFunction(arg) {
			      return index_js.execute(arg, [this._element]);
			    }
			    _getPopperConfig(attachment) {
			      const defaultBsPopperConfig = {
			        placement: attachment,
			        modifiers: [{
			          name: 'flip',
			          options: {
			            fallbackPlacements: this._config.fallbackPlacements
			          }
			        }, {
			          name: 'offset',
			          options: {
			            offset: this._getOffset()
			          }
			        }, {
			          name: 'preventOverflow',
			          options: {
			            boundary: this._config.boundary
			          }
			        }, {
			          name: 'arrow',
			          options: {
			            element: `.${this.constructor.NAME}-arrow`
			          }
			        }, {
			          name: 'preSetPlacement',
			          enabled: true,
			          phase: 'beforeMain',
			          fn: data => {
			            // Pre-set Popper's placement attribute in order to read the arrow sizes properly.
			            // Otherwise, Popper mixes up the width and height dimensions since the initial arrow style is for top placement
			            this._getTipElement().setAttribute('data-popper-placement', data.state.placement);
			          }
			        }]
			      };
			      return {
			        ...defaultBsPopperConfig,
			        ...index_js.execute(this._config.popperConfig, [defaultBsPopperConfig])
			      };
			    }
			    _setListeners() {
			      const triggers = this._config.trigger.split(' ');
			      for (const trigger of triggers) {
			        if (trigger === 'click') {
			          EventHandler.on(this._element, this.constructor.eventName(EVENT_CLICK), this._config.selector, event => {
			            const context = this._initializeOnDelegatedTarget(event);
			            context.toggle();
			          });
			        } else if (trigger !== TRIGGER_MANUAL) {
			          const eventIn = trigger === TRIGGER_HOVER ? this.constructor.eventName(EVENT_MOUSEENTER) : this.constructor.eventName(EVENT_FOCUSIN);
			          const eventOut = trigger === TRIGGER_HOVER ? this.constructor.eventName(EVENT_MOUSELEAVE) : this.constructor.eventName(EVENT_FOCUSOUT);
			          EventHandler.on(this._element, eventIn, this._config.selector, event => {
			            const context = this._initializeOnDelegatedTarget(event);
			            context._activeTrigger[event.type === 'focusin' ? TRIGGER_FOCUS : TRIGGER_HOVER] = true;
			            context._enter();
			          });
			          EventHandler.on(this._element, eventOut, this._config.selector, event => {
			            const context = this._initializeOnDelegatedTarget(event);
			            context._activeTrigger[event.type === 'focusout' ? TRIGGER_FOCUS : TRIGGER_HOVER] = context._element.contains(event.relatedTarget);
			            context._leave();
			          });
			        }
			      }
			      this._hideModalHandler = () => {
			        if (this._element) {
			          this.hide();
			        }
			      };
			      EventHandler.on(this._element.closest(SELECTOR_MODAL), EVENT_MODAL_HIDE, this._hideModalHandler);
			    }
			    _fixTitle() {
			      const title = this._element.getAttribute('title');
			      if (!title) {
			        return;
			      }
			      if (!this._element.getAttribute('aria-label') && !this._element.textContent.trim()) {
			        this._element.setAttribute('aria-label', title);
			      }
			      this._element.setAttribute('data-bs-original-title', title); // DO NOT USE IT. Is only for backwards compatibility
			      this._element.removeAttribute('title');
			    }
			    _enter() {
			      if (this._isShown() || this._isHovered) {
			        this._isHovered = true;
			        return;
			      }
			      this._isHovered = true;
			      this._setTimeout(() => {
			        if (this._isHovered) {
			          this.show();
			        }
			      }, this._config.delay.show);
			    }
			    _leave() {
			      if (this._isWithActiveTrigger()) {
			        return;
			      }
			      this._isHovered = false;
			      this._setTimeout(() => {
			        if (!this._isHovered) {
			          this.hide();
			        }
			      }, this._config.delay.hide);
			    }
			    _setTimeout(handler, timeout) {
			      clearTimeout(this._timeout);
			      this._timeout = setTimeout(handler, timeout);
			    }
			    _isWithActiveTrigger() {
			      return Object.values(this._activeTrigger).includes(true);
			    }
			    _getConfig(config) {
			      const dataAttributes = Manipulator.getDataAttributes(this._element);
			      for (const dataAttribute of Object.keys(dataAttributes)) {
			        if (DISALLOWED_ATTRIBUTES.has(dataAttribute)) {
			          delete dataAttributes[dataAttribute];
			        }
			      }
			      config = {
			        ...dataAttributes,
			        ...(typeof config === 'object' && config ? config : {})
			      };
			      config = this._mergeConfigObj(config);
			      config = this._configAfterMerge(config);
			      this._typeCheckConfig(config);
			      return config;
			    }
			    _configAfterMerge(config) {
			      config.container = config.container === false ? document.body : index_js.getElement(config.container);
			      if (typeof config.delay === 'number') {
			        config.delay = {
			          show: config.delay,
			          hide: config.delay
			        };
			      }
			      if (typeof config.title === 'number') {
			        config.title = config.title.toString();
			      }
			      if (typeof config.content === 'number') {
			        config.content = config.content.toString();
			      }
			      return config;
			    }
			    _getDelegateConfig() {
			      const config = {};
			      for (const [key, value] of Object.entries(this._config)) {
			        if (this.constructor.Default[key] !== value) {
			          config[key] = value;
			        }
			      }
			      config.selector = false;
			      config.trigger = 'manual';

			      // In the future can be replaced with:
			      // const keysWithDifferentValues = Object.entries(this._config).filter(entry => this.constructor.Default[entry[0]] !== this._config[entry[0]])
			      // `Object.fromEntries(keysWithDifferentValues)`
			      return config;
			    }
			    _disposePopper() {
			      if (this._popper) {
			        this._popper.destroy();
			        this._popper = null;
			      }
			      if (this.tip) {
			        this.tip.remove();
			        this.tip = null;
			      }
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Tooltip.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (typeof data[config] === 'undefined') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config]();
			      });
			    }
			  }

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Tooltip);
			  return Tooltip;
			}); 
		} (tooltip$2));
		return tooltip$2.exports;
	}

	/*!
	  * Bootstrap popover.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var popover$1 = popover$2.exports;

	var hasRequiredPopover;

	function requirePopover () {
		if (hasRequiredPopover) return popover$2.exports;
		hasRequiredPopover = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireTooltip(), requireUtil()) ;
			})(popover$1, function (Tooltip, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap popover.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'popover';
			  const SELECTOR_TITLE = '.popover-header';
			  const SELECTOR_CONTENT = '.popover-body';
			  const Default = {
			    ...Tooltip.Default,
			    content: '',
			    offset: [0, 8],
			    placement: 'right',
			    template: '<div class="popover" role="tooltip">' + '<div class="popover-arrow"></div>' + '<h3 class="popover-header"></h3>' + '<div class="popover-body"></div>' + '</div>',
			    trigger: 'click'
			  };
			  const DefaultType = {
			    ...Tooltip.DefaultType,
			    content: '(null|string|element|function)'
			  };

			  /**
			   * Class definition
			   */

			  class Popover extends Tooltip {
			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Overrides
			    _isWithContent() {
			      return this._getTitle() || this._getContent();
			    }

			    // Private
			    _getContentForTemplate() {
			      return {
			        [SELECTOR_TITLE]: this._getTitle(),
			        [SELECTOR_CONTENT]: this._getContent()
			      };
			    }
			    _getContent() {
			      return this._resolvePossibleFunction(this._config.content);
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Popover.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (typeof data[config] === 'undefined') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config]();
			      });
			    }
			  }

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Popover);
			  return Popover;
			}); 
		} (popover$2));
		return popover$2.exports;
	}

	var popoverExports = requirePopover();
	var popover = /*@__PURE__*/getDefaultExportFromCjs(popoverExports);

	var scrollspy$2 = {exports: {}};

	/*!
	  * Bootstrap scrollspy.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var scrollspy$1 = scrollspy$2.exports;

	var hasRequiredScrollspy;

	function requireScrollspy () {
		if (hasRequiredScrollspy) return scrollspy$2.exports;
		hasRequiredScrollspy = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireSelectorEngine(), requireUtil()) ;
			})(scrollspy$1, function (BaseComponent, EventHandler, SelectorEngine, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap scrollspy.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'scrollspy';
			  const DATA_KEY = 'bs.scrollspy';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const DATA_API_KEY = '.data-api';
			  const EVENT_ACTIVATE = `activate${EVENT_KEY}`;
			  const EVENT_CLICK = `click${EVENT_KEY}`;
			  const EVENT_LOAD_DATA_API = `load${EVENT_KEY}${DATA_API_KEY}`;
			  const CLASS_NAME_DROPDOWN_ITEM = 'dropdown-item';
			  const CLASS_NAME_ACTIVE = 'active';
			  const SELECTOR_DATA_SPY = '[data-bs-spy="scroll"]';
			  const SELECTOR_TARGET_LINKS = '[href]';
			  const SELECTOR_NAV_LIST_GROUP = '.nav, .list-group';
			  const SELECTOR_NAV_LINKS = '.nav-link';
			  const SELECTOR_NAV_ITEMS = '.nav-item';
			  const SELECTOR_LIST_ITEMS = '.list-group-item';
			  const SELECTOR_LINK_ITEMS = `${SELECTOR_NAV_LINKS}, ${SELECTOR_NAV_ITEMS} > ${SELECTOR_NAV_LINKS}, ${SELECTOR_LIST_ITEMS}`;
			  const SELECTOR_DROPDOWN = '.dropdown';
			  const SELECTOR_DROPDOWN_TOGGLE = '.dropdown-toggle';
			  const Default = {
			    offset: null,
			    // TODO: v6 @deprecated, keep it for backwards compatibility reasons
			    rootMargin: '0px 0px -25%',
			    smoothScroll: false,
			    target: null,
			    threshold: [0.1, 0.5, 1]
			  };
			  const DefaultType = {
			    offset: '(number|null)',
			    // TODO v6 @deprecated, keep it for backwards compatibility reasons
			    rootMargin: 'string',
			    smoothScroll: 'boolean',
			    target: 'element',
			    threshold: 'array'
			  };

			  /**
			   * Class definition
			   */

			  class ScrollSpy extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);

			      // this._element is the observablesContainer and config.target the menu links wrapper
			      this._targetLinks = new Map();
			      this._observableSections = new Map();
			      this._rootElement = getComputedStyle(this._element).overflowY === 'visible' ? null : this._element;
			      this._activeTarget = null;
			      this._observer = null;
			      this._previousScrollData = {
			        visibleEntryTop: 0,
			        parentScrollTop: 0
			      };
			      this.refresh(); // initialize
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    refresh() {
			      this._initializeTargetsAndObservables();
			      this._maybeEnableSmoothScroll();
			      if (this._observer) {
			        this._observer.disconnect();
			      } else {
			        this._observer = this._getNewObserver();
			      }
			      for (const section of this._observableSections.values()) {
			        this._observer.observe(section);
			      }
			    }
			    dispose() {
			      this._observer.disconnect();
			      super.dispose();
			    }

			    // Private
			    _configAfterMerge(config) {
			      // TODO: on v6 target should be given explicitly & remove the {target: 'ss-target'} case
			      config.target = index_js.getElement(config.target) || document.body;

			      // TODO: v6 Only for backwards compatibility reasons. Use rootMargin only
			      config.rootMargin = config.offset ? `${config.offset}px 0px -30%` : config.rootMargin;
			      if (typeof config.threshold === 'string') {
			        config.threshold = config.threshold.split(',').map(value => Number.parseFloat(value));
			      }
			      return config;
			    }
			    _maybeEnableSmoothScroll() {
			      if (!this._config.smoothScroll) {
			        return;
			      }

			      // unregister any previous listeners
			      EventHandler.off(this._config.target, EVENT_CLICK);
			      EventHandler.on(this._config.target, EVENT_CLICK, SELECTOR_TARGET_LINKS, event => {
			        const observableSection = this._observableSections.get(event.target.hash);
			        if (observableSection) {
			          event.preventDefault();
			          const root = this._rootElement || window;
			          const height = observableSection.offsetTop - this._element.offsetTop;
			          if (root.scrollTo) {
			            root.scrollTo({
			              top: height,
			              behavior: 'smooth'
			            });
			            return;
			          }

			          // Chrome 60 doesn't support `scrollTo`
			          root.scrollTop = height;
			        }
			      });
			    }
			    _getNewObserver() {
			      const options = {
			        root: this._rootElement,
			        threshold: this._config.threshold,
			        rootMargin: this._config.rootMargin
			      };
			      return new IntersectionObserver(entries => this._observerCallback(entries), options);
			    }

			    // The logic of selection
			    _observerCallback(entries) {
			      const targetElement = entry => this._targetLinks.get(`#${entry.target.id}`);
			      const activate = entry => {
			        this._previousScrollData.visibleEntryTop = entry.target.offsetTop;
			        this._process(targetElement(entry));
			      };
			      const parentScrollTop = (this._rootElement || document.documentElement).scrollTop;
			      const userScrollsDown = parentScrollTop >= this._previousScrollData.parentScrollTop;
			      this._previousScrollData.parentScrollTop = parentScrollTop;
			      for (const entry of entries) {
			        if (!entry.isIntersecting) {
			          this._activeTarget = null;
			          this._clearActiveClass(targetElement(entry));
			          continue;
			        }
			        const entryIsLowerThanPrevious = entry.target.offsetTop >= this._previousScrollData.visibleEntryTop;
			        // if we are scrolling down, pick the bigger offsetTop
			        if (userScrollsDown && entryIsLowerThanPrevious) {
			          activate(entry);
			          // if parent isn't scrolled, let's keep the first visible item, breaking the iteration
			          if (!parentScrollTop) {
			            return;
			          }
			          continue;
			        }

			        // if we are scrolling up, pick the smallest offsetTop
			        if (!userScrollsDown && !entryIsLowerThanPrevious) {
			          activate(entry);
			        }
			      }
			    }
			    _initializeTargetsAndObservables() {
			      this._targetLinks = new Map();
			      this._observableSections = new Map();
			      const targetLinks = SelectorEngine.find(SELECTOR_TARGET_LINKS, this._config.target);
			      for (const anchor of targetLinks) {
			        // ensure that the anchor has an id and is not disabled
			        if (!anchor.hash || index_js.isDisabled(anchor)) {
			          continue;
			        }
			        const observableSection = SelectorEngine.findOne(decodeURI(anchor.hash), this._element);

			        // ensure that the observableSection exists & is visible
			        if (index_js.isVisible(observableSection)) {
			          this._targetLinks.set(decodeURI(anchor.hash), anchor);
			          this._observableSections.set(anchor.hash, observableSection);
			        }
			      }
			    }
			    _process(target) {
			      if (this._activeTarget === target) {
			        return;
			      }
			      this._clearActiveClass(this._config.target);
			      this._activeTarget = target;
			      target.classList.add(CLASS_NAME_ACTIVE);
			      this._activateParents(target);
			      EventHandler.trigger(this._element, EVENT_ACTIVATE, {
			        relatedTarget: target
			      });
			    }
			    _activateParents(target) {
			      // Activate dropdown parents
			      if (target.classList.contains(CLASS_NAME_DROPDOWN_ITEM)) {
			        SelectorEngine.findOne(SELECTOR_DROPDOWN_TOGGLE, target.closest(SELECTOR_DROPDOWN)).classList.add(CLASS_NAME_ACTIVE);
			        return;
			      }
			      for (const listGroup of SelectorEngine.parents(target, SELECTOR_NAV_LIST_GROUP)) {
			        // Set triggered links parents as active
			        // With both <ul> and <nav> markup a parent is the previous sibling of any nav ancestor
			        for (const item of SelectorEngine.prev(listGroup, SELECTOR_LINK_ITEMS)) {
			          item.classList.add(CLASS_NAME_ACTIVE);
			        }
			      }
			    }
			    _clearActiveClass(parent) {
			      parent.classList.remove(CLASS_NAME_ACTIVE);
			      const activeNodes = SelectorEngine.find(`${SELECTOR_TARGET_LINKS}.${CLASS_NAME_ACTIVE}`, parent);
			      for (const node of activeNodes) {
			        node.classList.remove(CLASS_NAME_ACTIVE);
			      }
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = ScrollSpy.getOrCreateInstance(this, config);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config]();
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(window, EVENT_LOAD_DATA_API, () => {
			    for (const spy of SelectorEngine.find(SELECTOR_DATA_SPY)) {
			      ScrollSpy.getOrCreateInstance(spy);
			    }
			  });

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(ScrollSpy);
			  return ScrollSpy;
			}); 
		} (scrollspy$2));
		return scrollspy$2.exports;
	}

	var scrollspyExports = requireScrollspy();
	var scrollspy = /*@__PURE__*/getDefaultExportFromCjs(scrollspyExports);

	var tab$2 = {exports: {}};

	/*!
	  * Bootstrap tab.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var tab$1 = tab$2.exports;

	var hasRequiredTab;

	function requireTab () {
		if (hasRequiredTab) return tab$2.exports;
		hasRequiredTab = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireSelectorEngine(), requireUtil()) ;
			})(tab$1, function (BaseComponent, EventHandler, SelectorEngine, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap tab.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'tab';
			  const DATA_KEY = 'bs.tab';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}`;
			  const EVENT_KEYDOWN = `keydown${EVENT_KEY}`;
			  const EVENT_LOAD_DATA_API = `load${EVENT_KEY}`;
			  const ARROW_LEFT_KEY = 'ArrowLeft';
			  const ARROW_RIGHT_KEY = 'ArrowRight';
			  const ARROW_UP_KEY = 'ArrowUp';
			  const ARROW_DOWN_KEY = 'ArrowDown';
			  const HOME_KEY = 'Home';
			  const END_KEY = 'End';
			  const CLASS_NAME_ACTIVE = 'active';
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_DROPDOWN = 'dropdown';
			  const SELECTOR_DROPDOWN_TOGGLE = '.dropdown-toggle';
			  const SELECTOR_DROPDOWN_MENU = '.dropdown-menu';
			  const NOT_SELECTOR_DROPDOWN_TOGGLE = `:not(${SELECTOR_DROPDOWN_TOGGLE})`;
			  const SELECTOR_TAB_PANEL = '.list-group, .nav, [role="tablist"]';
			  const SELECTOR_OUTER = '.nav-item, .list-group-item';
			  const SELECTOR_INNER = `.nav-link${NOT_SELECTOR_DROPDOWN_TOGGLE}, .list-group-item${NOT_SELECTOR_DROPDOWN_TOGGLE}, [role="tab"]${NOT_SELECTOR_DROPDOWN_TOGGLE}`;
			  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="tab"], [data-bs-toggle="pill"], [data-bs-toggle="list"]'; // TODO: could only be `tab` in v6
			  const SELECTOR_INNER_ELEM = `${SELECTOR_INNER}, ${SELECTOR_DATA_TOGGLE}`;
			  const SELECTOR_DATA_TOGGLE_ACTIVE = `.${CLASS_NAME_ACTIVE}[data-bs-toggle="tab"], .${CLASS_NAME_ACTIVE}[data-bs-toggle="pill"], .${CLASS_NAME_ACTIVE}[data-bs-toggle="list"]`;

			  /**
			   * Class definition
			   */

			  class Tab extends BaseComponent {
			    constructor(element) {
			      super(element);
			      this._parent = this._element.closest(SELECTOR_TAB_PANEL);
			      if (!this._parent) {
			        return;
			        // TODO: should throw exception in v6
			        // throw new TypeError(`${element.outerHTML} has not a valid parent ${SELECTOR_INNER_ELEM}`)
			      }

			      // Set up initial aria attributes
			      this._setInitialAttributes(this._parent, this._getChildren());
			      EventHandler.on(this._element, EVENT_KEYDOWN, event => this._keydown(event));
			    }

			    // Getters
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    show() {
			      // Shows this elem and deactivate the active sibling if exists
			      const innerElem = this._element;
			      if (this._elemIsActive(innerElem)) {
			        return;
			      }

			      // Search for active tab on same parent to deactivate it
			      const active = this._getActiveElem();
			      const hideEvent = active ? EventHandler.trigger(active, EVENT_HIDE, {
			        relatedTarget: innerElem
			      }) : null;
			      const showEvent = EventHandler.trigger(innerElem, EVENT_SHOW, {
			        relatedTarget: active
			      });
			      if (showEvent.defaultPrevented || hideEvent && hideEvent.defaultPrevented) {
			        return;
			      }
			      this._deactivate(active, innerElem);
			      this._activate(innerElem, active);
			    }

			    // Private
			    _activate(element, relatedElem) {
			      if (!element) {
			        return;
			      }
			      element.classList.add(CLASS_NAME_ACTIVE);
			      this._activate(SelectorEngine.getElementFromSelector(element)); // Search and activate/show the proper section

			      const complete = () => {
			        if (element.getAttribute('role') !== 'tab') {
			          element.classList.add(CLASS_NAME_SHOW);
			          return;
			        }
			        element.removeAttribute('tabindex');
			        element.setAttribute('aria-selected', true);
			        this._toggleDropDown(element, true);
			        EventHandler.trigger(element, EVENT_SHOWN, {
			          relatedTarget: relatedElem
			        });
			      };
			      this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE));
			    }
			    _deactivate(element, relatedElem) {
			      if (!element) {
			        return;
			      }
			      element.classList.remove(CLASS_NAME_ACTIVE);
			      element.blur();
			      this._deactivate(SelectorEngine.getElementFromSelector(element)); // Search and deactivate the shown section too

			      const complete = () => {
			        if (element.getAttribute('role') !== 'tab') {
			          element.classList.remove(CLASS_NAME_SHOW);
			          return;
			        }
			        element.setAttribute('aria-selected', false);
			        element.setAttribute('tabindex', '-1');
			        this._toggleDropDown(element, false);
			        EventHandler.trigger(element, EVENT_HIDDEN, {
			          relatedTarget: relatedElem
			        });
			      };
			      this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE));
			    }
			    _keydown(event) {
			      if (![ARROW_LEFT_KEY, ARROW_RIGHT_KEY, ARROW_UP_KEY, ARROW_DOWN_KEY, HOME_KEY, END_KEY].includes(event.key)) {
			        return;
			      }
			      event.stopPropagation(); // stopPropagation/preventDefault both added to support up/down keys without scrolling the page
			      event.preventDefault();
			      const children = this._getChildren().filter(element => !index_js.isDisabled(element));
			      let nextActiveElement;
			      if ([HOME_KEY, END_KEY].includes(event.key)) {
			        nextActiveElement = children[event.key === HOME_KEY ? 0 : children.length - 1];
			      } else {
			        const isNext = [ARROW_RIGHT_KEY, ARROW_DOWN_KEY].includes(event.key);
			        nextActiveElement = index_js.getNextActiveElement(children, event.target, isNext, true);
			      }
			      if (nextActiveElement) {
			        nextActiveElement.focus({
			          preventScroll: true
			        });
			        Tab.getOrCreateInstance(nextActiveElement).show();
			      }
			    }
			    _getChildren() {
			      // collection of inner elements
			      return SelectorEngine.find(SELECTOR_INNER_ELEM, this._parent);
			    }
			    _getActiveElem() {
			      return this._getChildren().find(child => this._elemIsActive(child)) || null;
			    }
			    _setInitialAttributes(parent, children) {
			      this._setAttributeIfNotExists(parent, 'role', 'tablist');
			      for (const child of children) {
			        this._setInitialAttributesOnChild(child);
			      }
			    }
			    _setInitialAttributesOnChild(child) {
			      child = this._getInnerElement(child);
			      const isActive = this._elemIsActive(child);
			      const outerElem = this._getOuterElement(child);
			      child.setAttribute('aria-selected', isActive);
			      if (outerElem !== child) {
			        this._setAttributeIfNotExists(outerElem, 'role', 'presentation');
			      }
			      if (!isActive) {
			        child.setAttribute('tabindex', '-1');
			      }
			      this._setAttributeIfNotExists(child, 'role', 'tab');

			      // set attributes to the related panel too
			      this._setInitialAttributesOnTargetPanel(child);
			    }
			    _setInitialAttributesOnTargetPanel(child) {
			      const target = SelectorEngine.getElementFromSelector(child);
			      if (!target) {
			        return;
			      }
			      this._setAttributeIfNotExists(target, 'role', 'tabpanel');
			      if (child.id) {
			        this._setAttributeIfNotExists(target, 'aria-labelledby', `${child.id}`);
			      }
			    }
			    _toggleDropDown(element, open) {
			      const outerElem = this._getOuterElement(element);
			      if (!outerElem.classList.contains(CLASS_DROPDOWN)) {
			        return;
			      }
			      const toggle = (selector, className) => {
			        const element = SelectorEngine.findOne(selector, outerElem);
			        if (element) {
			          element.classList.toggle(className, open);
			        }
			      };
			      toggle(SELECTOR_DROPDOWN_TOGGLE, CLASS_NAME_ACTIVE);
			      toggle(SELECTOR_DROPDOWN_MENU, CLASS_NAME_SHOW);
			      outerElem.setAttribute('aria-expanded', open);
			    }
			    _setAttributeIfNotExists(element, attribute, value) {
			      if (!element.hasAttribute(attribute)) {
			        element.setAttribute(attribute, value);
			      }
			    }
			    _elemIsActive(elem) {
			      return elem.classList.contains(CLASS_NAME_ACTIVE);
			    }

			    // Try to get the inner element (usually the .nav-link)
			    _getInnerElement(elem) {
			      return elem.matches(SELECTOR_INNER_ELEM) ? elem : SelectorEngine.findOne(SELECTOR_INNER_ELEM, elem);
			    }

			    // Try to get the outer element (usually the .nav-item)
			    _getOuterElement(elem) {
			      return elem.closest(SELECTOR_OUTER) || elem;
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Tab.getOrCreateInstance(this);
			        if (typeof config !== 'string') {
			          return;
			        }
			        if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
			          throw new TypeError(`No method named "${config}"`);
			        }
			        data[config]();
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
			    if (['A', 'AREA'].includes(this.tagName)) {
			      event.preventDefault();
			    }
			    if (index_js.isDisabled(this)) {
			      return;
			    }
			    Tab.getOrCreateInstance(this).show();
			  });

			  /**
			   * Initialize on focus
			   */
			  EventHandler.on(window, EVENT_LOAD_DATA_API, () => {
			    for (const element of SelectorEngine.find(SELECTOR_DATA_TOGGLE_ACTIVE)) {
			      Tab.getOrCreateInstance(element);
			    }
			  });
			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Tab);
			  return Tab;
			}); 
		} (tab$2));
		return tab$2.exports;
	}

	var tabExports = requireTab();
	var tab = /*@__PURE__*/getDefaultExportFromCjs(tabExports);

	var toast$2 = {exports: {}};

	/*!
	  * Bootstrap toast.js v5.3.3 (https://getbootstrap.com/)
	  * Copyright 2011-2024 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
	  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
	  */
	var toast$1 = toast$2.exports;

	var hasRequiredToast;

	function requireToast () {
		if (hasRequiredToast) return toast$2.exports;
		hasRequiredToast = 1;
		(function (module, exports) {
			(function (global, factory) {
			  module.exports = factory(requireBaseComponent(), requireEventHandler(), requireComponentFunctions(), requireUtil()) ;
			})(toast$1, function (BaseComponent, EventHandler, componentFunctions_js, index_js) {

			  /**
			   * --------------------------------------------------------------------------
			   * Bootstrap toast.js
			   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
			   * --------------------------------------------------------------------------
			   */

			  /**
			   * Constants
			   */
			  const NAME = 'toast';
			  const DATA_KEY = 'bs.toast';
			  const EVENT_KEY = `.${DATA_KEY}`;
			  const EVENT_MOUSEOVER = `mouseover${EVENT_KEY}`;
			  const EVENT_MOUSEOUT = `mouseout${EVENT_KEY}`;
			  const EVENT_FOCUSIN = `focusin${EVENT_KEY}`;
			  const EVENT_FOCUSOUT = `focusout${EVENT_KEY}`;
			  const EVENT_HIDE = `hide${EVENT_KEY}`;
			  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
			  const EVENT_SHOW = `show${EVENT_KEY}`;
			  const EVENT_SHOWN = `shown${EVENT_KEY}`;
			  const CLASS_NAME_FADE = 'fade';
			  const CLASS_NAME_HIDE = 'hide'; // @deprecated - kept here only for backwards compatibility
			  const CLASS_NAME_SHOW = 'show';
			  const CLASS_NAME_SHOWING = 'showing';
			  const DefaultType = {
			    animation: 'boolean',
			    autohide: 'boolean',
			    delay: 'number'
			  };
			  const Default = {
			    animation: true,
			    autohide: true,
			    delay: 5000
			  };

			  /**
			   * Class definition
			   */

			  class Toast extends BaseComponent {
			    constructor(element, config) {
			      super(element, config);
			      this._timeout = null;
			      this._hasMouseInteraction = false;
			      this._hasKeyboardInteraction = false;
			      this._setListeners();
			    }

			    // Getters
			    static get Default() {
			      return Default;
			    }
			    static get DefaultType() {
			      return DefaultType;
			    }
			    static get NAME() {
			      return NAME;
			    }

			    // Public
			    show() {
			      const showEvent = EventHandler.trigger(this._element, EVENT_SHOW);
			      if (showEvent.defaultPrevented) {
			        return;
			      }
			      this._clearTimeout();
			      if (this._config.animation) {
			        this._element.classList.add(CLASS_NAME_FADE);
			      }
			      const complete = () => {
			        this._element.classList.remove(CLASS_NAME_SHOWING);
			        EventHandler.trigger(this._element, EVENT_SHOWN);
			        this._maybeScheduleHide();
			      };
			      this._element.classList.remove(CLASS_NAME_HIDE); // @deprecated
			      index_js.reflow(this._element);
			      this._element.classList.add(CLASS_NAME_SHOW, CLASS_NAME_SHOWING);
			      this._queueCallback(complete, this._element, this._config.animation);
			    }
			    hide() {
			      if (!this.isShown()) {
			        return;
			      }
			      const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE);
			      if (hideEvent.defaultPrevented) {
			        return;
			      }
			      const complete = () => {
			        this._element.classList.add(CLASS_NAME_HIDE); // @deprecated
			        this._element.classList.remove(CLASS_NAME_SHOWING, CLASS_NAME_SHOW);
			        EventHandler.trigger(this._element, EVENT_HIDDEN);
			      };
			      this._element.classList.add(CLASS_NAME_SHOWING);
			      this._queueCallback(complete, this._element, this._config.animation);
			    }
			    dispose() {
			      this._clearTimeout();
			      if (this.isShown()) {
			        this._element.classList.remove(CLASS_NAME_SHOW);
			      }
			      super.dispose();
			    }
			    isShown() {
			      return this._element.classList.contains(CLASS_NAME_SHOW);
			    }

			    // Private

			    _maybeScheduleHide() {
			      if (!this._config.autohide) {
			        return;
			      }
			      if (this._hasMouseInteraction || this._hasKeyboardInteraction) {
			        return;
			      }
			      this._timeout = setTimeout(() => {
			        this.hide();
			      }, this._config.delay);
			    }
			    _onInteraction(event, isInteracting) {
			      switch (event.type) {
			        case 'mouseover':
			        case 'mouseout':
			          {
			            this._hasMouseInteraction = isInteracting;
			            break;
			          }
			        case 'focusin':
			        case 'focusout':
			          {
			            this._hasKeyboardInteraction = isInteracting;
			            break;
			          }
			      }
			      if (isInteracting) {
			        this._clearTimeout();
			        return;
			      }
			      const nextElement = event.relatedTarget;
			      if (this._element === nextElement || this._element.contains(nextElement)) {
			        return;
			      }
			      this._maybeScheduleHide();
			    }
			    _setListeners() {
			      EventHandler.on(this._element, EVENT_MOUSEOVER, event => this._onInteraction(event, true));
			      EventHandler.on(this._element, EVENT_MOUSEOUT, event => this._onInteraction(event, false));
			      EventHandler.on(this._element, EVENT_FOCUSIN, event => this._onInteraction(event, true));
			      EventHandler.on(this._element, EVENT_FOCUSOUT, event => this._onInteraction(event, false));
			    }
			    _clearTimeout() {
			      clearTimeout(this._timeout);
			      this._timeout = null;
			    }

			    // Static
			    static jQueryInterface(config) {
			      return this.each(function () {
			        const data = Toast.getOrCreateInstance(this, config);
			        if (typeof config === 'string') {
			          if (typeof data[config] === 'undefined') {
			            throw new TypeError(`No method named "${config}"`);
			          }
			          data[config](this);
			        }
			      });
			    }
			  }

			  /**
			   * Data API implementation
			   */

			  componentFunctions_js.enableDismissTrigger(Toast);

			  /**
			   * jQuery
			   */

			  index_js.defineJQueryPlugin(Toast);
			  return Toast;
			}); 
		} (toast$2));
		return toast$2.exports;
	}

	var toastExports = requireToast();
	var toast = /*@__PURE__*/getDefaultExportFromCjs(toastExports);

	var tooltipExports = requireTooltip();
	var tooltip = /*@__PURE__*/getDefaultExportFromCjs(tooltipExports);

	window.bootstrap = {
	  Modal,
	  Alert,
	  Dropdown,
	  Collapse,
	  Offcanvas
	};

	/**
	 * File skip-link-focus-fix.js.
	 *
	 * Helps with accessibility for keyboard only users.
	 *
	 * Learn more: https://git.io/vWdr2
	 */
	(function () {
	  var isWebkit = navigator.userAgent.toLowerCase().indexOf('webkit') > -1,
	    isOpera = navigator.userAgent.toLowerCase().indexOf('opera') > -1,
	    isIe = navigator.userAgent.toLowerCase().indexOf('msie') > -1;
	  if ((isWebkit || isOpera || isIe) && document.getElementById && window.addEventListener) {
	    window.addEventListener('hashchange', function () {
	      var id = location.hash.substring(1),
	        element;
	      if (!/^[A-z0-9_-]+$/.test(id)) {
	        return;
	      }
	      element = document.getElementById(id);
	      if (element) {
	        if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
	          element.tabIndex = -1;
	        }
	        element.focus();
	      }
	    }, false);
	  }
	})();

	// Add your custom JS here.
	// AOS.init({
	//   easing: 'ease-out',
	//   once: true
	// });

	/*
	(function() {
	  // Hide header on scroll
	  var doc = document.documentElement;
	  var w = window;

	  var prevScroll = w.scrollY || doc.scrollTop;
	  var curScroll;
	  var direction = 0;
	  var prevDirection = 0;

	  var header = document.getElementById('wrapper-navbar');

	  var checkScroll = function() {
	      // Find the direction of scroll (0 - initial, 1 - up, 2 - down)
	      curScroll = w.scrollY || doc.scrollTop;
	      if (curScroll > prevScroll) {
	          // Scrolled down
	          direction = 2;
	      } else if (curScroll < prevScroll) {
	          // Scrolled up
	          direction = 1;
	      }

	      if (direction !== prevDirection) {
	          toggleHeader(direction, curScroll);
	      }

	      prevScroll = curScroll;
	  };

	  var toggleHeader = function(direction, curScroll) {
	      if (direction === 2 && curScroll > 125) {
	          // Replace 52 with the height of your header in px
	          if (!document.getElementById('navbar').classList.contains('show')) {
	              header.classList.add('hide');
	              prevDirection = direction;
	          }
	      } else if (direction === 1) {
	          header.classList.remove('hide');
	          prevDirection = direction;
	      }
	  };

	  window.addEventListener('scroll', checkScroll);

	  // Header background
	  document.addEventListener('scroll', function() {
	      var nav = document.getElementById('navbar');
	    //   var primaryNav = document.getElementById('primaryNav');
	    //   if (!primaryNav.classList.contains('show')) {
	    //       nav.classList.toggle('scrolled', window.scrollY > nav.offsetHeight);
	    //   }
	      document.querySelectorAll('.dropdown-menu').forEach(function(dropdown) {
	          dropdown.classList.remove('show');
	      });
	      document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
	          toggle.classList.remove('show');
	          toggle.blur();
	      });
	  });

	  // Toggle navdark class on nav toggle button click
	//   document.getElementById('navToggle').addEventListener('click', function() {
	//       var nav = document.getElementById('navbar');
	//       nav.classList.toggle('navdark');
	//   });

	  // Hide menu on click outside
	//   document.body.addEventListener('click', function(event) {
	//       if (!event.target.closest('.navbar')) {
	//           var navbarCollapse = document.querySelector('.navbar .navbar-collapse');
	//           if (navbarCollapse) {
	//               var collapseInstance = bootstrap.Collapse.getInstance(navbarCollapse);
	//               if (collapseInstance) {
	//                   collapseInstance.hide();
	//               }
	//           }
	//       }
	//   });
	})();
	*/

	document.addEventListener("DOMContentLoaded", function () {
	  const parallaxImages = document.querySelectorAll("[data-parallax]");
	  function updateParallax() {
	    parallaxImages.forEach(image => {
	      let rect = image.parentElement.getBoundingClientRect();
	      let speed = 0.2; // Adjust this for more or less effect
	      let containerHeight = image.parentElement.offsetHeight;

	      // Calculate correct parallax offset to prevent gaps
	      let offset = (rect.top - window.innerHeight * 0.5) * speed;

	      // Ensure the image fully covers the container and does not create gaps
	      if (Math.abs(offset) < containerHeight * 0.5) {
	        image.style.transform = `translateY(${offset}px)`;
	      }
	    });
	    requestAnimationFrame(updateParallax);
	  }
	  updateParallax(); // Ensure correct positioning before first scroll
	});

	exports.Alert = Alert;
	exports.Button = button;
	exports.Carousel = carousel;
	exports.Collapse = Collapse;
	exports.Dropdown = Dropdown;
	exports.Modal = Modal;
	exports.Offcanvas = Offcanvas;
	exports.Popover = popover;
	exports.Scrollspy = scrollspy;
	exports.Tab = tab;
	exports.Toast = toast;
	exports.Tooltip = tooltip;

}));
//# sourceMappingURL=child-theme.js.map

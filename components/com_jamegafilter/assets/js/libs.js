/*! dustjs-linkedin - v2.7.2
* http://dustjs.com/
* Copyright (c) 2015 Aleksander Williams; Released under the MIT License */
(function (root, factory) {
  if (typeof define === 'function' && define.amd && define.amd.dust === true) {
    define('dust.core', [], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory();
  } else {
    root.dust = factory();
  }
}(this, function() {
  var dust = {
        "version": "2.7.2"
      },
      NONE = 'NONE', ERROR = 'ERROR', WARN = 'WARN', INFO = 'INFO', DEBUG = 'DEBUG',
      EMPTY_FUNC = function() {};

  dust.config = {
    whitespace: false,
    amd: false,
    cjs: false,
    cache: true
  };

  // Directive aliases to minify code
  dust._aliases = {
    "write": "w",
    "end": "e",
    "map": "m",
    "render": "r",
    "reference": "f",
    "section": "s",
    "exists": "x",
    "notexists": "nx",
    "block": "b",
    "partial": "p",
    "helper": "h"
  };

  (function initLogging() {
    /*global process, console*/
    var loggingLevels = { DEBUG: 0, INFO: 1, WARN: 2, ERROR: 3, NONE: 4 },
        consoleLog,
        log;

    if (typeof console !== 'undefined' && console.log) {
      consoleLog = console.log;
      if(typeof consoleLog === 'function') {
        log = function() {
          consoleLog.apply(console, arguments);
        };
      } else {
        log = function() {
          consoleLog(Array.prototype.slice.apply(arguments).join(' '));
        };
      }
    } else {
      log = EMPTY_FUNC;
    }

    /**
     * Filters messages based on `dust.debugLevel`.
     * This default implementation will print to the console if it exists.
     * @param {String|Error} message the message to print/throw
     * @param {String} type the severity of the message(ERROR, WARN, INFO, or DEBUG)
     * @public
     */
    dust.log = function(message, type) {
      type = type || INFO;
      if (loggingLevels[type] >= loggingLevels[dust.debugLevel]) {
        log('[DUST:' + type + ']', message);
      }
    };

    dust.debugLevel = NONE;
    if(typeof process !== 'undefined' && process.env && /\bdust\b/.test(process.env.DEBUG)) {
      dust.debugLevel = DEBUG;
    }

  }());

  dust.helpers = {};

  dust.cache = {};

  dust.register = function(name, tmpl) {
    if (!name) {
      return;
    }
    tmpl.templateName = name;
    if (dust.config.cache !== false) {
      dust.cache[name] = tmpl;
    }
  };

  dust.render = function(nameOrTemplate, context, callback) {
    var chunk = new Stub(callback).head;
    try {
      load(nameOrTemplate, chunk, context).end();
    } catch (err) {
      chunk.setError(err);
    }
  };

  dust.stream = function(nameOrTemplate, context) {
    var stream = new Stream(),
        chunk = stream.head;
    dust.nextTick(function() {
      try {
        load(nameOrTemplate, chunk, context).end();
      } catch (err) {
        chunk.setError(err);
      }
    });
    return stream;
  };

  /**
   * Extracts a template function (body_0) from whatever is passed.
   * @param nameOrTemplate {*} Could be:
   *   - the name of a template to load from cache
   *   - a CommonJS-compiled template (a function with a `template` property)
   *   - a template function
   * @param loadFromCache {Boolean} if false, don't look in the cache
   * @return {Function} a template function, if found
   */
  function getTemplate(nameOrTemplate, loadFromCache/*=true*/) {
    if(!nameOrTemplate) {
      return;
    }
    if(typeof nameOrTemplate === 'function' && nameOrTemplate.template) {
      // Sugar away CommonJS module templates
      return nameOrTemplate.template;
    }
    if(dust.isTemplateFn(nameOrTemplate)) {
      // Template functions passed directly
      return nameOrTemplate;
    }
    if(loadFromCache !== false) {
      // Try loading a template with this name from cache
      return dust.cache[nameOrTemplate];
    }
  }

  function load(nameOrTemplate, chunk, context) {
    if(!nameOrTemplate) {
      return chunk.setError(new Error('No template or template name provided to render'));
    }

    var template = getTemplate(nameOrTemplate, dust.config.cache);

    if (template) {
      return template(chunk, Context.wrap(context, template.templateName));
    } else {
      if (dust.onLoad) {
        return chunk.map(function(chunk) {
          // Alias just so it's easier to read that this would always be a name
          var name = nameOrTemplate;
          // Three possible scenarios for a successful callback:
          //   - `require(nameOrTemplate)(dust); cb()`
          //   - `src = readFile('src.dust'); cb(null, src)`
          //   - `compiledTemplate = require(nameOrTemplate)(dust); cb(null, compiledTemplate)`
          function done(err, srcOrTemplate) {
            var template;
            if (err) {
              return chunk.setError(err);
            }
            // Prefer a template that is passed via callback over the cached version.
            template = getTemplate(srcOrTemplate, false) || getTemplate(name, dust.config.cache);
            if (!template) {
              // It's a template string, compile it and register under `name`
              if(dust.compile) {
                template = dust.loadSource(dust.compile(srcOrTemplate, name));
              } else {
                return chunk.setError(new Error('Dust compiler not available'));
              }
            }
            template(chunk, Context.wrap(context, template.templateName)).end();
          }

          if(dust.onLoad.length === 3) {
            dust.onLoad(name, context.options, done);
          } else {
            dust.onLoad(name, done);
          }
        });
      }
      return chunk.setError(new Error('Template Not Found: ' + nameOrTemplate));
    }
  }

  dust.loadSource = function(source) {
    /*jshint evil:true*/
    return eval(source);
  };

  if (Array.isArray) {
    dust.isArray = Array.isArray;
  } else {
    dust.isArray = function(arr) {
      return Object.prototype.toString.call(arr) === '[object Array]';
    };
  }

  dust.nextTick = (function() {
    return function(callback) {
      setTimeout(callback, 0);
    };
  })();

  /**
   * Dust has its own rules for what is "empty"-- which is not the same as falsy.
   * Empty arrays, null, and undefined are empty
   */
  dust.isEmpty = function(value) {
    if (value === 0) {
      return false;
    }
    if (dust.isArray(value) && !value.length) {
      return true;
    }
    return !value;
  };

  dust.isEmptyObject = function(obj) {
    var key;
    if (obj === null) {
      return false;
    }
    if (obj === undefined) {
      return false;
    }
    if (obj.length > 0) {
      return false;
    }
    for (key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        return false;
      }
    }
    return true;
  };

  dust.isTemplateFn = function(elem) {
    return typeof elem === 'function' &&
           elem.__dustBody;
  };

  /**
   * Decide somewhat-naively if something is a Thenable.
   * @param elem {*} object to inspect
   * @return {Boolean} is `elem` a Thenable?
   */
  dust.isThenable = function(elem) {
    return elem &&
           typeof elem === 'object' &&
           typeof elem.then === 'function';
  };

  /**
   * Decide very naively if something is a Stream.
   * @param elem {*} object to inspect
   * @return {Boolean} is `elem` a Stream?
   */
  dust.isStreamable = function(elem) {
    return elem &&
           typeof elem.on === 'function' &&
           typeof elem.pipe === 'function';
  };

  // apply the filter chain and return the output string
  dust.filter = function(string, auto, filters, context) {
    var i, len, name, filter;
    if (filters) {
      for (i = 0, len = filters.length; i < len; i++) {
        name = filters[i];
        if (!name.length) {
          continue;
        }
        filter = dust.filters[name];
        if (name === 's') {
          auto = null;
        } else if (typeof filter === 'function') {
          string = filter(string, context);
        } else {
          dust.log('Invalid filter `' + name + '`', WARN);
        }
      }
    }
    // by default always apply the h filter, unless asked to unescape with |s
    if (auto) {
      string = dust.filters[auto](string, context);
    }
    return string;
  };

  dust.filters = {
    h: function(value) { return dust.escapeHtml(value); },
    j: function(value) { return dust.escapeJs(value); },
    u: encodeURI,
    uc: encodeURIComponent,
    js: function(value) { return dust.escapeJSON(value); },
    jp: function(value) {
      if (!JSON) {dust.log('JSON is undefined; could not parse `' + value + '`', WARN);
        return value;
      } else {
        return JSON.parse(value);
      }
    }
  };

  function Context(stack, global, options, blocks, templateName) {
    if(stack !== undefined && !(stack instanceof Stack)) {
      stack = new Stack(stack);
    }
    this.stack = stack;
    this.global = global;
    this.options = options;
    this.blocks = blocks;
    this.templateName = templateName;
  }

  dust.makeBase = dust.context = function(global, options) {
    return new Context(undefined, global, options);
  };

  /**
   * Factory function that creates a closure scope around a Thenable-callback.
   * Returns a function that can be passed to a Thenable that will resume a
   * Context lookup once the Thenable resolves with new data, adding that new
   * data to the lookup stack.
   */
  function getWithResolvedData(ctx, cur, down) {
    return function(data) {
      return ctx.push(data)._get(cur, down);
    };
  }

  Context.wrap = function(context, name) {
    if (context instanceof Context) {
      return context;
    }
    return new Context(context, {}, {}, null, name);
  };

  /**
   * Public API for getting a value from the context.
   * @method get
   * @param {string|array} path The path to the value. Supported formats are:
   * 'key'
   * 'path.to.key'
   * '.path.to.key'
   * ['path', 'to', 'key']
   * ['key']
   * @param {boolean} [cur=false] Boolean which determines if the search should be limited to the
   * current context (true), or if get should search in parent contexts as well (false).
   * @public
   * @returns {string|object}
   */
  Context.prototype.get = function(path, cur) {
    if (typeof path === 'string') {
      if (path[0] === '.') {
        cur = true;
        path = path.substr(1);
      }
      path = path.split('.');
    }
    return this._get(cur, path);
  };

  /**
   * Get a value from the context
   * @method _get
   * @param {boolean} cur Get only from the current context
   * @param {array} down An array of each step in the path
   * @private
   * @return {string | object}
   */
  Context.prototype._get = function(cur, down) {
    var ctx = this.stack || {},
        i = 1,
        value, first, len, ctxThis, fn;

    first = down[0];
    len = down.length;

    if (cur && len === 0) {
      ctxThis = ctx;
      ctx = ctx.head;
    } else {
      if (!cur) {
        // Search up the stack for the first value
        while (ctx) {
          if (ctx.isObject) {
            ctxThis = ctx.head;
            value = ctx.head[first];
            if (value !== undefined) {
              break;
            }
          }
          ctx = ctx.tail;
        }

        // Try looking in the global context if we haven't found anything yet
        if (value !== undefined) {
          ctx = value;
        } else {
          ctx = this.global && this.global[first];
        }
      } else if (ctx) {
        // if scope is limited by a leading dot, don't search up the tree
        if(ctx.head) {
          ctx = ctx.head[first];
        } else {
          // context's head is empty, value we are searching for is not defined
          ctx = undefined;
        }
      }

      while (ctx && i < len) {
        if (dust.isThenable(ctx)) {
          // Bail early by returning a Thenable for the remainder of the search tree
          return ctx.then(getWithResolvedData(this, cur, down.slice(i)));
        }
        ctxThis = ctx;
        ctx = ctx[down[i]];
        i++;
      }
    }

    if (typeof ctx === 'function') {
      fn = function() {
        try {
          return ctx.apply(ctxThis, arguments);
        } catch (err) {
          dust.log(err, ERROR);
          throw err;
        }
      };
      fn.__dustBody = !!ctx.__dustBody;
      return fn;
    } else {
      if (ctx === undefined) {
        dust.log('Cannot find reference `{' + down.join('.') + '}` in template `' + this.getTemplateName() + '`', INFO);
      }
      return ctx;
    }
  };

  Context.prototype.getPath = function(cur, down) {
    return this._get(cur, down);
  };

  Context.prototype.push = function(head, idx, len) {
    if(head === undefined) {
      dust.log("Not pushing an undefined variable onto the context", INFO);
      return this;
    }
    return this.rebase(new Stack(head, this.stack, idx, len));
  };

  Context.prototype.pop = function() {
    var head = this.current();
    this.stack = this.stack && this.stack.tail;
    return head;
  };

  Context.prototype.rebase = function(head) {
    return new Context(head, this.global, this.options, this.blocks, this.getTemplateName());
  };

  Context.prototype.clone = function() {
    var context = this.rebase();
    context.stack = this.stack;
    return context;
  };

  Context.prototype.current = function() {
    return this.stack && this.stack.head;
  };

  Context.prototype.getBlock = function(key) {
    var blocks, len, fn;

    if (typeof key === 'function') {
      key = key(new Chunk(), this).data.join('');
    }

    blocks = this.blocks;

    if (!blocks) {
      dust.log('No blocks for context `' + key + '` in template `' + this.getTemplateName() + '`', DEBUG);
      return false;
    }

    len = blocks.length;
    while (len--) {
      fn = blocks[len][key];
      if (fn) {
        return fn;
      }
    }

    dust.log('Malformed template `' + this.getTemplateName() + '` was missing one or more blocks.');
    return false;
  };

  Context.prototype.shiftBlocks = function(locals) {
    var blocks = this.blocks,
        newBlocks;

    if (locals) {
      if (!blocks) {
        newBlocks = [locals];
      } else {
        newBlocks = blocks.concat([locals]);
      }
      return new Context(this.stack, this.global, this.options, newBlocks, this.getTemplateName());
    }
    return this;
  };

  Context.prototype.resolve = function(body) {
    var chunk;

    if(typeof body !== 'function') {
      return body;
    }
    chunk = new Chunk().render(body, this);
    if(chunk instanceof Chunk) {
      return chunk.data.join(''); // ie7 perf
    }
    return chunk;
  };

  Context.prototype.getTemplateName = function() {
    return this.templateName;
  };

  function Stack(head, tail, idx, len) {
    this.tail = tail;
    this.isObject = head && typeof head === 'object';
    this.head = head;
    this.index = idx;
    this.of = len;
  }

  function Stub(callback) {
    this.head = new Chunk(this);
    this.callback = callback;
    this.out = '';
  }

  Stub.prototype.flush = function() {
    var chunk = this.head;

    while (chunk) {
      if (chunk.flushable) {
        this.out += chunk.data.join(''); //ie7 perf
      } else if (chunk.error) {
        this.callback(chunk.error);
        dust.log('Rendering failed with error `' + chunk.error + '`', ERROR);
        this.flush = EMPTY_FUNC;
        return;
      } else {
        return;
      }
      chunk = chunk.next;
      this.head = chunk;
    }
    this.callback(null, this.out);
  };

  /**
   * Creates an interface sort of like a Streams2 ReadableStream.
   */
  function Stream() {
    this.head = new Chunk(this);
  }

  Stream.prototype.flush = function() {
    var chunk = this.head;

    while(chunk) {
      if (chunk.flushable) {
        this.emit('data', chunk.data.join('')); //ie7 perf
      } else if (chunk.error) {
        this.emit('error', chunk.error);
        this.emit('end');
        dust.log('Streaming failed with error `' + chunk.error + '`', ERROR);
        this.flush = EMPTY_FUNC;
        return;
      } else {
        return;
      }
      chunk = chunk.next;
      this.head = chunk;
    }
    this.emit('end');
  };

  /**
   * Executes listeners for `type` by passing data. Note that this is different from a
   * Node stream, which can pass an arbitrary number of arguments
   * @return `true` if event had listeners, `false` otherwise
   */
  Stream.prototype.emit = function(type, data) {
    var events = this.events || {},
        handlers = events[type] || [],
        i, l;

    if (!handlers.length) {
      dust.log('Stream broadcasting, but no listeners for `' + type + '`', DEBUG);
      return false;
    }

    handlers = handlers.slice(0);
    for (i = 0, l = handlers.length; i < l; i++) {
      handlers[i](data);
    }
    return true;
  };

  Stream.prototype.on = function(type, callback) {
    var events = this.events = this.events || {},
        handlers = events[type] = events[type] || [];

    if(typeof callback !== 'function') {
      dust.log('No callback function provided for `' + type + '` event listener', WARN);
    } else {
      handlers.push(callback);
    }
    return this;
  };

  /**
   * Pipes to a WritableStream. Note that backpressure isn't implemented,
   * so we just write as fast as we can.
   * @param stream {WritableStream}
   * @return self
   */
  Stream.prototype.pipe = function(stream) {
    if(typeof stream.write !== 'function' ||
       typeof stream.end !== 'function') {
      dust.log('Incompatible stream passed to `pipe`', WARN);
      return this;
    }

    var destEnded = false;

    if(typeof stream.emit === 'function') {
      stream.emit('pipe', this);
    }

    if(typeof stream.on === 'function') {
      stream.on('error', function() {
        destEnded = true;
      });
    }

    return this
    .on('data', function(data) {
      if(destEnded) {
        return;
      }
      try {
        stream.write(data, 'utf8');
      } catch (err) {
        dust.log(err, ERROR);
      }
    })
    .on('end', function() {
      if(destEnded) {
        return;
      }
      try {
        stream.end();
        destEnded = true;
      } catch (err) {
        dust.log(err, ERROR);
      }
    });
  };

  function Chunk(root, next, taps) {
    this.root = root;
    this.next = next;
    this.data = []; //ie7 perf
    this.flushable = false;
    this.taps = taps;
  }

  Chunk.prototype.write = function(data) {
    var taps = this.taps;

    if (taps) {
      data = taps.go(data);
    }
    this.data.push(data);
    return this;
  };

  Chunk.prototype.end = function(data) {
    if (data) {
      this.write(data);
    }
    this.flushable = true;
    this.root.flush();
    return this;
  };

  Chunk.prototype.map = function(callback) {
    var cursor = new Chunk(this.root, this.next, this.taps),
        branch = new Chunk(this.root, cursor, this.taps);

    this.next = branch;
    this.flushable = true;
    try {
      callback(branch);
    } catch(err) {
      dust.log(err, ERROR);
      branch.setError(err);
    }
    return cursor;
  };

  Chunk.prototype.tap = function(tap) {
    var taps = this.taps;

    if (taps) {
      this.taps = taps.push(tap);
    } else {
      this.taps = new Tap(tap);
    }
    return this;
  };

  Chunk.prototype.untap = function() {
    this.taps = this.taps.tail;
    return this;
  };

  Chunk.prototype.render = function(body, context) {
    return body(this, context);
  };

  Chunk.prototype.reference = function(elem, context, auto, filters) {
    if (typeof elem === 'function') {
      elem = elem.apply(context.current(), [this, context, null, {auto: auto, filters: filters}]);
      if (elem instanceof Chunk) {
        return elem;
      } else {
        return this.reference(elem, context, auto, filters);
      }
    }
    if (dust.isThenable(elem)) {
      return this.await(elem, context, null, auto, filters);
    } else if (dust.isStreamable(elem)) {
      return this.stream(elem, context, null, auto, filters);
    } else if (!dust.isEmpty(elem)) {
      return this.write(dust.filter(elem, auto, filters, context));
    } else {
      return this;
    }
  };

  Chunk.prototype.section = function(elem, context, bodies, params) {
    var body = bodies.block,
        skip = bodies['else'],
        chunk = this,
        i, len, head;

    if (typeof elem === 'function' && !dust.isTemplateFn(elem)) {
      try {
        elem = elem.apply(context.current(), [this, context, bodies, params]);
      } catch(err) {
        dust.log(err, ERROR);
        return this.setError(err);
      }
      // Functions that return chunks are assumed to have handled the chunk manually.
      // Make that chunk the current one and go to the next method in the chain.
      if (elem instanceof Chunk) {
        return elem;
      }
    }

    if (dust.isEmptyObject(bodies)) {
      // No bodies to render, and we've already invoked any function that was available in
      // hopes of returning a Chunk.
      return chunk;
    }

    if (!dust.isEmptyObject(params)) {
      context = context.push(params);
    }

    /*
    Dust's default behavior is to enumerate over the array elem, passing each object in the array to the block.
    When elem resolves to a value or object instead of an array, Dust sets the current context to the value
    and renders the block one time.
    */
    if (dust.isArray(elem)) {
      if (body) {
        len = elem.length;
        if (len > 0) {
          head = context.stack && context.stack.head || {};
          head.$len = len;
          for (i = 0; i < len; i++) {
            head.$idx = i;
            chunk = body(chunk, context.push(elem[i], i, len));
          }
          head.$idx = undefined;
          head.$len = undefined;
          return chunk;
        } else if (skip) {
          return skip(this, context);
        }
      }
    } else if (dust.isThenable(elem)) {
      return this.await(elem, context, bodies);
    } else if (dust.isStreamable(elem)) {
      return this.stream(elem, context, bodies);
    } else if (elem === true) {
     // true is truthy but does not change context
      if (body) {
        return body(this, context);
      }
    } else if (elem || elem === 0) {
       // everything that evaluates to true are truthy ( e.g. Non-empty strings and Empty objects are truthy. )
       // zero is truthy
       // for anonymous functions that did not returns a chunk, truthiness is evaluated based on the return value
      if (body) {
        return body(this, context.push(elem));
      }
     // nonexistent, scalar false value, scalar empty string, null,
     // undefined are all falsy
    } else if (skip) {
      return skip(this, context);
    }
    dust.log('Section without corresponding key in template `' + context.getTemplateName() + '`', DEBUG);
    return this;
  };

  Chunk.prototype.exists = function(elem, context, bodies) {
    var body = bodies.block,
        skip = bodies['else'];

    if (!dust.isEmpty(elem)) {
      if (body) {
        return body(this, context);
      }
      dust.log('No block for exists check in template `' + context.getTemplateName() + '`', DEBUG);
    } else if (skip) {
      return skip(this, context);
    }
    return this;
  };

  Chunk.prototype.notexists = function(elem, context, bodies) {
    var body = bodies.block,
        skip = bodies['else'];

    if (dust.isEmpty(elem)) {
      if (body) {
        return body(this, context);
      }
      dust.log('No block for not-exists check in template `' + context.getTemplateName() + '`', DEBUG);
    } else if (skip) {
      return skip(this, context);
    }
    return this;
  };

  Chunk.prototype.block = function(elem, context, bodies) {
    var body = elem || bodies.block;

    if (body) {
      return body(this, context);
    }
    return this;
  };

  Chunk.prototype.partial = function(elem, context, partialContext, params) {
    var head;

    if(params === undefined) {
      // Compatibility for < 2.7.0 where `partialContext` did not exist
      params = partialContext;
      partialContext = context;
    }

    if (!dust.isEmptyObject(params)) {
      partialContext = partialContext.clone();
      head = partialContext.pop();
      partialContext = partialContext.push(params)
                                     .push(head);
    }

    if (dust.isTemplateFn(elem)) {
      // The eventual result of evaluating `elem` is a partial name
      // Load the partial after getting its name and end the async chunk
      return this.capture(elem, context, function(name, chunk) {
        partialContext.templateName = name;
        load(name, chunk, partialContext).end();
      });
    } else {
      partialContext.templateName = elem;
      return load(elem, this, partialContext);
    }
  };

  Chunk.prototype.helper = function(name, context, bodies, params, auto) {
    var chunk = this,
        filters = params.filters,
        ret;

    // Pre-2.7.1 compat: if auto is undefined, it's an old template. Automatically escape
    if (auto === undefined) {
      auto = 'h';
    }

    // handle invalid helpers, similar to invalid filters
    if(dust.helpers[name]) {
      try {
        ret = dust.helpers[name](chunk, context, bodies, params);
        if (ret instanceof Chunk) {
          return ret;
        }
        if(typeof filters === 'string') {
          filters = filters.split('|');
        }
        if (!dust.isEmptyObject(bodies)) {
          return chunk.section(ret, context, bodies, params);
        }
        // Helpers act slightly differently from functions in context in that they will act as
        // a reference if they are self-closing (due to grammar limitations)
        // In the Chunk.await function we check to make sure bodies is null before acting as a reference
        return chunk.reference(ret, context, auto, filters);
      } catch(err) {
        dust.log('Error in helper `' + name + '`: ' + err.message, ERROR);
        return chunk.setError(err);
      }
    } else {
      dust.log('Helper `' + name + '` does not exist', WARN);
      return chunk;
    }
  };

  /**
   * Reserve a chunk to be evaluated once a thenable is resolved or rejected
   * @param thenable {Thenable} the target thenable to await
   * @param context {Context} context to use to render the deferred chunk
   * @param bodies {Object} must contain a "body", may contain an "error"
   * @param auto {String} automatically apply this filter if the Thenable is a reference
   * @param filters {Array} apply these filters if the Thenable is a reference
   * @return {Chunk}
   */
  Chunk.prototype.await = function(thenable, context, bodies, auto, filters) {
    return this.map(function(chunk) {
      thenable.then(function(data) {
        if (bodies) {
          chunk = chunk.section(data, context, bodies);
        } else {
          // Actually a reference. Self-closing sections don't render
          chunk = chunk.reference(data, context, auto, filters);
        }
        chunk.end();
      }, function(err) {
        var errorBody = bodies && bodies.error;
        if(errorBody) {
          chunk.render(errorBody, context.push(err)).end();
        } else {
          dust.log('Unhandled promise rejection in `' + context.getTemplateName() + '`', INFO);
          chunk.end();
        }
      });
    });
  };

  /**
   * Reserve a chunk to be evaluated with the contents of a streamable.
   * Currently an error event will bomb out the stream. Once an error
   * is received, we push it to an {:error} block if one exists, and log otherwise,
   * then stop listening to the stream.
   * @param streamable {Streamable} the target streamable that will emit events
   * @param context {Context} context to use to render each thunk
   * @param bodies {Object} must contain a "body", may contain an "error"
   * @return {Chunk}
   */
  Chunk.prototype.stream = function(stream, context, bodies, auto, filters) {
    var body = bodies && bodies.block,
        errorBody = bodies && bodies.error;
    return this.map(function(chunk) {
      var ended = false;
      stream
        .on('data', function data(thunk) {
          if(ended) {
            return;
          }
          if(body) {
            // Fork a new chunk out of the blockstream so that we can flush it independently
            chunk = chunk.map(function(chunk) {
              chunk.render(body, context.push(thunk)).end();
            });
          } else if(!bodies) {
            // When actually a reference, don't fork, just write into the master async chunk
            chunk = chunk.reference(thunk, context, auto, filters);
          }
        })
        .on('error', function error(err) {
          if(ended) {
            return;
          }
          if(errorBody) {
            chunk.render(errorBody, context.push(err));
          } else {
            dust.log('Unhandled stream error in `' + context.getTemplateName() + '`', INFO);
          }
          if(!ended) {
            ended = true;
            chunk.end();
          }
        })
        .on('end', function end() {
          if(!ended) {
            ended = true;
            chunk.end();
          }
        });
    });
  };

  Chunk.prototype.capture = function(body, context, callback) {
    return this.map(function(chunk) {
      var stub = new Stub(function(err, out) {
        if (err) {
          chunk.setError(err);
        } else {
          callback(out, chunk);
        }
      });
      body(stub.head, context).end();
    });
  };

  Chunk.prototype.setError = function(err) {
    this.error = err;
    this.root.flush();
    return this;
  };

  // Chunk aliases
  for(var f in Chunk.prototype) {
    if(dust._aliases[f]) {
      Chunk.prototype[dust._aliases[f]] = Chunk.prototype[f];
    }
  }

  function Tap(head, tail) {
    this.head = head;
    this.tail = tail;
  }

  Tap.prototype.push = function(tap) {
    return new Tap(tap, this);
  };

  Tap.prototype.go = function(value) {
    var tap = this;

    while(tap) {
      value = tap.head(value);
      tap = tap.tail;
    }
    return value;
  };

  var HCHARS = /[&<>"']/,
      AMP    = /&/g,
      LT     = /</g,
      GT     = />/g,
      QUOT   = /\"/g,
      SQUOT  = /\'/g;

  dust.escapeHtml = function(s) {
    if (typeof s === "string" || (s && typeof s.toString === "function")) {
      if (typeof s !== "string") {
        s = s.toString();
      }
      if (!HCHARS.test(s)) {
        return s;
      }
      return s.replace(AMP,'&amp;').replace(LT,'&lt;').replace(GT,'&gt;').replace(QUOT,'&quot;').replace(SQUOT, '&#39;');
    }
    return s;
  };

  var BS = /\\/g,
      FS = /\//g,
      CR = /\r/g,
      LS = /\u2028/g,
      PS = /\u2029/g,
      NL = /\n/g,
      LF = /\f/g,
      SQ = /'/g,
      DQ = /"/g,
      TB = /\t/g;

  dust.escapeJs = function(s) {
    if (typeof s === 'string') {
      return s
        .replace(BS, '\\\\')
        .replace(FS, '\\/')
        .replace(DQ, '\\"')
        .replace(SQ, '\\\'')
        .replace(CR, '\\r')
        .replace(LS, '\\u2028')
        .replace(PS, '\\u2029')
        .replace(NL, '\\n')
        .replace(LF, '\\f')
        .replace(TB, '\\t');
    }
    return s;
  };

  dust.escapeJSON = function(o) {
    if (!JSON) {
      dust.log('JSON is undefined; could not escape `' + o + '`', WARN);
      return o;
    } else {
      return JSON.stringify(o)
        .replace(LS, '\\u2028')
        .replace(PS, '\\u2029')
        .replace(LT, '\\u003c');
    }
  };

  return dust;

}));

(function(root, factory) {
  if (typeof define === "function" && define.amd && define.amd.dust === true) {
    define("dust.parse", ["dust.core"], function(dust) {
      return factory(dust).parse;
    });
  } else if (typeof exports === 'object') {
    // in Node, require this file if we want to use the parser as a standalone module
    module.exports = factory(require('./dust'));
    // @see server file for parser methods exposed in node
  } else {
    // in the browser, store the factory output if we want to use the parser directly
    factory(root.dust);
  }
}(this, function(dust) {
  var parser = (function() {
  /*
   * Generated by PEG.js 0.8.0.
   *
   * http://pegjs.majda.cz/
   */

  function peg$subclass(child, parent) {
    function ctor() { this.constructor = child; }
    ctor.prototype = parent.prototype;
    child.prototype = new ctor();
  }

  function SyntaxError(message, expected, found, offset, line, column) {
    this.message  = message;
    this.expected = expected;
    this.found    = found;
    this.offset   = offset;
    this.line     = line;
    this.column   = column;

    this.name     = "SyntaxError";
  }

  peg$subclass(SyntaxError, Error);

  function parse(input) {
    var options = arguments.length > 1 ? arguments[1] : {},

        peg$FAILED = {},

        peg$startRuleFunctions = { start: peg$parsestart },
        peg$startRuleFunction  = peg$parsestart,

        peg$c0 = [],
        peg$c1 = function(p) {
            var body = ["body"].concat(p);
            return withPosition(body);
          },
        peg$c2 = { type: "other", description: "section" },
        peg$c3 = peg$FAILED,
        peg$c4 = null,
        peg$c5 = function(t, b, e, n) {
            if( (!n) || (t[1].text !== n.text) ) {
              error("Expected end tag for "+t[1].text+" but it was not found.");
            }
            return true;
          },
        peg$c6 = void 0,
        peg$c7 = function(t, b, e, n) {
            e.push(["param", ["literal", "block"], b]);
            t.push(e, ["filters"]);
            return withPosition(t)
          },
        peg$c8 = "/",
        peg$c9 = { type: "literal", value: "/", description: "\"/\"" },
        peg$c10 = function(t) {
            t.push(["bodies"], ["filters"]);
            return withPosition(t)
          },
        peg$c11 = /^[#?\^<+@%]/,
        peg$c12 = { type: "class", value: "[#?\\^<+@%]", description: "[#?\\^<+@%]" },
        peg$c13 = function(t, n, c, p) { return [t, n, c, p] },
        peg$c14 = { type: "other", description: "end tag" },
        peg$c15 = function(n) { return n },
        peg$c16 = ":",
        peg$c17 = { type: "literal", value: ":", description: "\":\"" },
        peg$c18 = function(n) {return n},
        peg$c19 = function(n) { return n ? ["context", n] : ["context"] },
        peg$c20 = { type: "other", description: "params" },
        peg$c21 = "=",
        peg$c22 = { type: "literal", value: "=", description: "\"=\"" },
        peg$c23 = function(k, v) {return ["param", ["literal", k], v]},
        peg$c24 = function(p) { return ["params"].concat(p) },
        peg$c25 = { type: "other", description: "bodies" },
        peg$c26 = function(p) { return ["bodies"].concat(p) },
        peg$c27 = { type: "other", description: "reference" },
        peg$c28 = function(n, f) { return withPosition(["reference", n, f]) },
        peg$c29 = { type: "other", description: "partial" },
        peg$c30 = ">",
        peg$c31 = { type: "literal", value: ">", description: "\">\"" },
        peg$c32 = "+",
        peg$c33 = { type: "literal", value: "+", description: "\"+\"" },
        peg$c34 = function(k) {return ["literal", k]},
        peg$c35 = function(s, n, c, p) {
            var key = (s === ">") ? "partial" : s;
            return withPosition([key, n, c, p])
          },
        peg$c36 = { type: "other", description: "filters" },
        peg$c37 = "|",
        peg$c38 = { type: "literal", value: "|", description: "\"|\"" },
        peg$c39 = function(f) { return ["filters"].concat(f) },
        peg$c40 = { type: "other", description: "special" },
        peg$c41 = "~",
        peg$c42 = { type: "literal", value: "~", description: "\"~\"" },
        peg$c43 = function(k) { return withPosition(["special", k]) },
        peg$c44 = { type: "other", description: "identifier" },
        peg$c45 = function(p) {
            var arr = ["path"].concat(p);
            arr.text = p[1].join('.').replace(/,line,\d+,col,\d+/g,'');
            return arr;
          },
        peg$c46 = function(k) {
            var arr = ["key", k];
            arr.text = k;
            return arr;
          },
        peg$c47 = { type: "other", description: "number" },
        peg$c48 = function(n) { return ['literal', n]; },
        peg$c49 = { type: "other", description: "float" },
        peg$c50 = ".",
        peg$c51 = { type: "literal", value: ".", description: "\".\"" },
        peg$c52 = function(l, r) { return parseFloat(l + "." + r); },
        peg$c53 = { type: "other", description: "unsigned_integer" },
        peg$c54 = /^[0-9]/,
        peg$c55 = { type: "class", value: "[0-9]", description: "[0-9]" },
        peg$c56 = function(digits) { return makeInteger(digits); },
        peg$c57 = { type: "other", description: "signed_integer" },
        peg$c58 = "-",
        peg$c59 = { type: "literal", value: "-", description: "\"-\"" },
        peg$c60 = function(sign, n) { return n * -1; },
        peg$c61 = { type: "other", description: "integer" },
        peg$c62 = { type: "other", description: "path" },
        peg$c63 = function(k, d) {
            d = d[0];
            if (k && d) {
              d.unshift(k);
              return withPosition([false, d])
            }
            return withPosition([true, d])
          },
        peg$c64 = function(d) {
            if (d.length > 0) {
              return withPosition([true, d[0]])
            }
            return withPosition([true, []])
          },
        peg$c65 = { type: "other", description: "key" },
        peg$c66 = /^[a-zA-Z_$]/,
        peg$c67 = { type: "class", value: "[a-zA-Z_$]", description: "[a-zA-Z_$]" },
        peg$c68 = /^[0-9a-zA-Z_$\-]/,
        peg$c69 = { type: "class", value: "[0-9a-zA-Z_$\\-]", description: "[0-9a-zA-Z_$\\-]" },
        peg$c70 = function(h, t) { return h + t.join('') },
        peg$c71 = { type: "other", description: "array" },
        peg$c72 = function(n) {return n.join('')},
        peg$c73 = function(a) {return a; },
        peg$c74 = function(i, nk) { if(nk) { nk.unshift(i); } else {nk = [i] } return nk; },
        peg$c75 = { type: "other", description: "array_part" },
        peg$c76 = function(k) {return k},
        peg$c77 = function(d, a) { if (a) { return d.concat(a); } else { return d; } },
        peg$c78 = { type: "other", description: "inline" },
        peg$c79 = "\"",
        peg$c80 = { type: "literal", value: "\"", description: "\"\\\"\"" },
        peg$c81 = function() { return withPosition(["literal", ""]) },
        peg$c82 = function(l) { return withPosition(["literal", l]) },
        peg$c83 = function(p) { return withPosition(["body"].concat(p)) },
        peg$c84 = function(l) { return ["buffer", l] },
        peg$c85 = { type: "other", description: "buffer" },
        peg$c86 = function(e, w) { return withPosition(["format", e, w.join('')]) },
        peg$c87 = { type: "any", description: "any character" },
        peg$c88 = function(c) {return c},
        peg$c89 = function(b) { return withPosition(["buffer", b.join('')]) },
        peg$c90 = { type: "other", description: "literal" },
        peg$c91 = /^[^"]/,
        peg$c92 = { type: "class", value: "[^\"]", description: "[^\"]" },
        peg$c93 = function(b) { return b.join('') },
        peg$c94 = "\\\"",
        peg$c95 = { type: "literal", value: "\\\"", description: "\"\\\\\\\"\"" },
        peg$c96 = function() { return '"' },
        peg$c97 = { type: "other", description: "raw" },
        peg$c98 = "{`",
        peg$c99 = { type: "literal", value: "{`", description: "\"{`\"" },
        peg$c100 = "`}",
        peg$c101 = { type: "literal", value: "`}", description: "\"`}\"" },
        peg$c102 = function(character) {return character},
        peg$c103 = function(rawText) { return withPosition(["raw", rawText.join('')]) },
        peg$c104 = { type: "other", description: "comment" },
        peg$c105 = "{!",
        peg$c106 = { type: "literal", value: "{!", description: "\"{!\"" },
        peg$c107 = "!}",
        peg$c108 = { type: "literal", value: "!}", description: "\"!}\"" },
        peg$c109 = function(c) { return withPosition(["comment", c.join('')]) },
        peg$c110 = /^[#?\^><+%:@\/~%]/,
        peg$c111 = { type: "class", value: "[#?\\^><+%:@\\/~%]", description: "[#?\\^><+%:@\\/~%]" },
        peg$c112 = "{",
        peg$c113 = { type: "literal", value: "{", description: "\"{\"" },
        peg$c114 = "}",
        peg$c115 = { type: "literal", value: "}", description: "\"}\"" },
        peg$c116 = "[",
        peg$c117 = { type: "literal", value: "[", description: "\"[\"" },
        peg$c118 = "]",
        peg$c119 = { type: "literal", value: "]", description: "\"]\"" },
        peg$c120 = "\n",
        peg$c121 = { type: "literal", value: "\n", description: "\"\\n\"" },
        peg$c122 = "\r\n",
        peg$c123 = { type: "literal", value: "\r\n", description: "\"\\r\\n\"" },
        peg$c124 = "\r",
        peg$c125 = { type: "literal", value: "\r", description: "\"\\r\"" },
        peg$c126 = "\u2028",
        peg$c127 = { type: "literal", value: "\u2028", description: "\"\\u2028\"" },
        peg$c128 = "\u2029",
        peg$c129 = { type: "literal", value: "\u2029", description: "\"\\u2029\"" },
        peg$c130 = /^[\t\x0B\f \xA0\uFEFF]/,
        peg$c131 = { type: "class", value: "[\\t\\x0B\\f \\xA0\\uFEFF]", description: "[\\t\\x0B\\f \\xA0\\uFEFF]" },

        peg$currPos          = 0,
        peg$reportedPos      = 0,
        peg$cachedPos        = 0,
        peg$cachedPosDetails = { line: 1, column: 1, seenCR: false },
        peg$maxFailPos       = 0,
        peg$maxFailExpected  = [],
        peg$silentFails      = 0,

        peg$result;

    if ("startRule" in options) {
      if (!(options.startRule in peg$startRuleFunctions)) {
        throw new Error("Can't start parsing from rule \"" + options.startRule + "\".");
      }

      peg$startRuleFunction = peg$startRuleFunctions[options.startRule];
    }

    function text() {
      return input.substring(peg$reportedPos, peg$currPos);
    }

    function offset() {
      return peg$reportedPos;
    }

    function line() {
      return peg$computePosDetails(peg$reportedPos).line;
    }

    function column() {
      return peg$computePosDetails(peg$reportedPos).column;
    }

    function expected(description) {
      throw peg$buildException(
        null,
        [{ type: "other", description: description }],
        peg$reportedPos
      );
    }

    function error(message) {
      throw peg$buildException(message, null, peg$reportedPos);
    }

    function peg$computePosDetails(pos) {
      function advance(details, startPos, endPos) {
        var p, ch;

        for (p = startPos; p < endPos; p++) {
          ch = input.charAt(p);
          if (ch === "\n") {
            if (!details.seenCR) { details.line++; }
            details.column = 1;
            details.seenCR = false;
          } else if (ch === "\r" || ch === "\u2028" || ch === "\u2029") {
            details.line++;
            details.column = 1;
            details.seenCR = true;
          } else {
            details.column++;
            details.seenCR = false;
          }
        }
      }

      if (peg$cachedPos !== pos) {
        if (peg$cachedPos > pos) {
          peg$cachedPos = 0;
          peg$cachedPosDetails = { line: 1, column: 1, seenCR: false };
        }
        advance(peg$cachedPosDetails, peg$cachedPos, pos);
        peg$cachedPos = pos;
      }

      return peg$cachedPosDetails;
    }

    function peg$fail(expected) {
      if (peg$currPos < peg$maxFailPos) { return; }

      if (peg$currPos > peg$maxFailPos) {
        peg$maxFailPos = peg$currPos;
        peg$maxFailExpected = [];
      }

      peg$maxFailExpected.push(expected);
    }

    function peg$buildException(message, expected, pos) {
      function cleanupExpected(expected) {
        var i = 1;

        expected.sort(function(a, b) {
          if (a.description < b.description) {
            return -1;
          } else if (a.description > b.description) {
            return 1;
          } else {
            return 0;
          }
        });

        while (i < expected.length) {
          if (expected[i - 1] === expected[i]) {
            expected.splice(i, 1);
          } else {
            i++;
          }
        }
      }

      function buildMessage(expected, found) {
        function stringEscape(s) {
          function hex(ch) { return ch.charCodeAt(0).toString(16).toUpperCase(); }

          return s
            .replace(/\\/g,   '\\\\')
            .replace(/"/g,    '\\"')
            .replace(/\x08/g, '\\b')
            .replace(/\t/g,   '\\t')
            .replace(/\n/g,   '\\n')
            .replace(/\f/g,   '\\f')
            .replace(/\r/g,   '\\r')
            .replace(/[\x00-\x07\x0B\x0E\x0F]/g, function(ch) { return '\\x0' + hex(ch); })
            .replace(/[\x10-\x1F\x80-\xFF]/g,    function(ch) { return '\\x'  + hex(ch); })
            .replace(/[\u0180-\u0FFF]/g,         function(ch) { return '\\u0' + hex(ch); })
            .replace(/[\u1080-\uFFFF]/g,         function(ch) { return '\\u'  + hex(ch); });
        }

        var expectedDescs = new Array(expected.length),
            expectedDesc, foundDesc, i;

        for (i = 0; i < expected.length; i++) {
          expectedDescs[i] = expected[i].description;
        }

        expectedDesc = expected.length > 1
          ? expectedDescs.slice(0, -1).join(", ")
              + " or "
              + expectedDescs[expected.length - 1]
          : expectedDescs[0];

        foundDesc = found ? "\"" + stringEscape(found) + "\"" : "end of input";

        return "Expected " + expectedDesc + " but " + foundDesc + " found.";
      }

      var posDetails = peg$computePosDetails(pos),
          found      = pos < input.length ? input.charAt(pos) : null;

      if (expected !== null) {
        cleanupExpected(expected);
      }

      return new SyntaxError(
        message !== null ? message : buildMessage(expected, found),
        expected,
        found,
        pos,
        posDetails.line,
        posDetails.column
      );
    }

    function peg$parsestart() {
      var s0;

      s0 = peg$parsebody();

      return s0;
    }

    function peg$parsebody() {
      var s0, s1, s2;

      s0 = peg$currPos;
      s1 = [];
      s2 = peg$parsepart();
      while (s2 !== peg$FAILED) {
        s1.push(s2);
        s2 = peg$parsepart();
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c1(s1);
      }
      s0 = s1;

      return s0;
    }

    function peg$parsepart() {
      var s0;

      s0 = peg$parseraw();
      if (s0 === peg$FAILED) {
        s0 = peg$parsecomment();
        if (s0 === peg$FAILED) {
          s0 = peg$parsesection();
          if (s0 === peg$FAILED) {
            s0 = peg$parsepartial();
            if (s0 === peg$FAILED) {
              s0 = peg$parsespecial();
              if (s0 === peg$FAILED) {
                s0 = peg$parsereference();
                if (s0 === peg$FAILED) {
                  s0 = peg$parsebuffer();
                }
              }
            }
          }
        }
      }

      return s0;
    }

    function peg$parsesection() {
      var s0, s1, s2, s3, s4, s5, s6, s7;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parsesec_tag_start();
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$parsews();
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$parsews();
        }
        if (s2 !== peg$FAILED) {
          s3 = peg$parserd();
          if (s3 !== peg$FAILED) {
            s4 = peg$parsebody();
            if (s4 !== peg$FAILED) {
              s5 = peg$parsebodies();
              if (s5 !== peg$FAILED) {
                s6 = peg$parseend_tag();
                if (s6 === peg$FAILED) {
                  s6 = peg$c4;
                }
                if (s6 !== peg$FAILED) {
                  peg$reportedPos = peg$currPos;
                  s7 = peg$c5(s1, s4, s5, s6);
                  if (s7) {
                    s7 = peg$c6;
                  } else {
                    s7 = peg$c3;
                  }
                  if (s7 !== peg$FAILED) {
                    peg$reportedPos = s0;
                    s1 = peg$c7(s1, s4, s5, s6);
                    s0 = s1;
                  } else {
                    peg$currPos = s0;
                    s0 = peg$c3;
                  }
                } else {
                  peg$currPos = s0;
                  s0 = peg$c3;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      if (s0 === peg$FAILED) {
        s0 = peg$currPos;
        s1 = peg$parsesec_tag_start();
        if (s1 !== peg$FAILED) {
          s2 = [];
          s3 = peg$parsews();
          while (s3 !== peg$FAILED) {
            s2.push(s3);
            s3 = peg$parsews();
          }
          if (s2 !== peg$FAILED) {
            if (input.charCodeAt(peg$currPos) === 47) {
              s3 = peg$c8;
              peg$currPos++;
            } else {
              s3 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c9); }
            }
            if (s3 !== peg$FAILED) {
              s4 = peg$parserd();
              if (s4 !== peg$FAILED) {
                peg$reportedPos = s0;
                s1 = peg$c10(s1);
                s0 = s1;
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c2); }
      }

      return s0;
    }

    function peg$parsesec_tag_start() {
      var s0, s1, s2, s3, s4, s5, s6;

      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        if (peg$c11.test(input.charAt(peg$currPos))) {
          s2 = input.charAt(peg$currPos);
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c12); }
        }
        if (s2 !== peg$FAILED) {
          s3 = [];
          s4 = peg$parsews();
          while (s4 !== peg$FAILED) {
            s3.push(s4);
            s4 = peg$parsews();
          }
          if (s3 !== peg$FAILED) {
            s4 = peg$parseidentifier();
            if (s4 !== peg$FAILED) {
              s5 = peg$parsecontext();
              if (s5 !== peg$FAILED) {
                s6 = peg$parseparams();
                if (s6 !== peg$FAILED) {
                  peg$reportedPos = s0;
                  s1 = peg$c13(s2, s4, s5, s6);
                  s0 = s1;
                } else {
                  peg$currPos = s0;
                  s0 = peg$c3;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }

      return s0;
    }

    function peg$parseend_tag() {
      var s0, s1, s2, s3, s4, s5, s6;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 47) {
          s2 = peg$c8;
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c9); }
        }
        if (s2 !== peg$FAILED) {
          s3 = [];
          s4 = peg$parsews();
          while (s4 !== peg$FAILED) {
            s3.push(s4);
            s4 = peg$parsews();
          }
          if (s3 !== peg$FAILED) {
            s4 = peg$parseidentifier();
            if (s4 !== peg$FAILED) {
              s5 = [];
              s6 = peg$parsews();
              while (s6 !== peg$FAILED) {
                s5.push(s6);
                s6 = peg$parsews();
              }
              if (s5 !== peg$FAILED) {
                s6 = peg$parserd();
                if (s6 !== peg$FAILED) {
                  peg$reportedPos = s0;
                  s1 = peg$c15(s4);
                  s0 = s1;
                } else {
                  peg$currPos = s0;
                  s0 = peg$c3;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c14); }
      }

      return s0;
    }

    function peg$parsecontext() {
      var s0, s1, s2, s3;

      s0 = peg$currPos;
      s1 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 58) {
        s2 = peg$c16;
        peg$currPos++;
      } else {
        s2 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c17); }
      }
      if (s2 !== peg$FAILED) {
        s3 = peg$parseidentifier();
        if (s3 !== peg$FAILED) {
          peg$reportedPos = s1;
          s2 = peg$c18(s3);
          s1 = s2;
        } else {
          peg$currPos = s1;
          s1 = peg$c3;
        }
      } else {
        peg$currPos = s1;
        s1 = peg$c3;
      }
      if (s1 === peg$FAILED) {
        s1 = peg$c4;
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c19(s1);
      }
      s0 = s1;

      return s0;
    }

    function peg$parseparams() {
      var s0, s1, s2, s3, s4, s5, s6;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      s2 = peg$currPos;
      s3 = [];
      s4 = peg$parsews();
      if (s4 !== peg$FAILED) {
        while (s4 !== peg$FAILED) {
          s3.push(s4);
          s4 = peg$parsews();
        }
      } else {
        s3 = peg$c3;
      }
      if (s3 !== peg$FAILED) {
        s4 = peg$parsekey();
        if (s4 !== peg$FAILED) {
          if (input.charCodeAt(peg$currPos) === 61) {
            s5 = peg$c21;
            peg$currPos++;
          } else {
            s5 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c22); }
          }
          if (s5 !== peg$FAILED) {
            s6 = peg$parsenumber();
            if (s6 === peg$FAILED) {
              s6 = peg$parseidentifier();
              if (s6 === peg$FAILED) {
                s6 = peg$parseinline();
              }
            }
            if (s6 !== peg$FAILED) {
              peg$reportedPos = s2;
              s3 = peg$c23(s4, s6);
              s2 = s3;
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$c3;
      }
      while (s2 !== peg$FAILED) {
        s1.push(s2);
        s2 = peg$currPos;
        s3 = [];
        s4 = peg$parsews();
        if (s4 !== peg$FAILED) {
          while (s4 !== peg$FAILED) {
            s3.push(s4);
            s4 = peg$parsews();
          }
        } else {
          s3 = peg$c3;
        }
        if (s3 !== peg$FAILED) {
          s4 = peg$parsekey();
          if (s4 !== peg$FAILED) {
            if (input.charCodeAt(peg$currPos) === 61) {
              s5 = peg$c21;
              peg$currPos++;
            } else {
              s5 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c22); }
            }
            if (s5 !== peg$FAILED) {
              s6 = peg$parsenumber();
              if (s6 === peg$FAILED) {
                s6 = peg$parseidentifier();
                if (s6 === peg$FAILED) {
                  s6 = peg$parseinline();
                }
              }
              if (s6 !== peg$FAILED) {
                peg$reportedPos = s2;
                s3 = peg$c23(s4, s6);
                s2 = s3;
              } else {
                peg$currPos = s2;
                s2 = peg$c3;
              }
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c24(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c20); }
      }

      return s0;
    }

    function peg$parsebodies() {
      var s0, s1, s2, s3, s4, s5, s6, s7;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      s2 = peg$currPos;
      s3 = peg$parseld();
      if (s3 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 58) {
          s4 = peg$c16;
          peg$currPos++;
        } else {
          s4 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c17); }
        }
        if (s4 !== peg$FAILED) {
          s5 = peg$parsekey();
          if (s5 !== peg$FAILED) {
            s6 = peg$parserd();
            if (s6 !== peg$FAILED) {
              s7 = peg$parsebody();
              if (s7 !== peg$FAILED) {
                peg$reportedPos = s2;
                s3 = peg$c23(s5, s7);
                s2 = s3;
              } else {
                peg$currPos = s2;
                s2 = peg$c3;
              }
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$c3;
      }
      while (s2 !== peg$FAILED) {
        s1.push(s2);
        s2 = peg$currPos;
        s3 = peg$parseld();
        if (s3 !== peg$FAILED) {
          if (input.charCodeAt(peg$currPos) === 58) {
            s4 = peg$c16;
            peg$currPos++;
          } else {
            s4 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c17); }
          }
          if (s4 !== peg$FAILED) {
            s5 = peg$parsekey();
            if (s5 !== peg$FAILED) {
              s6 = peg$parserd();
              if (s6 !== peg$FAILED) {
                s7 = peg$parsebody();
                if (s7 !== peg$FAILED) {
                  peg$reportedPos = s2;
                  s3 = peg$c23(s5, s7);
                  s2 = s3;
                } else {
                  peg$currPos = s2;
                  s2 = peg$c3;
                }
              } else {
                peg$currPos = s2;
                s2 = peg$c3;
              }
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c26(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c25); }
      }

      return s0;
    }

    function peg$parsereference() {
      var s0, s1, s2, s3, s4;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        s2 = peg$parseidentifier();
        if (s2 !== peg$FAILED) {
          s3 = peg$parsefilters();
          if (s3 !== peg$FAILED) {
            s4 = peg$parserd();
            if (s4 !== peg$FAILED) {
              peg$reportedPos = s0;
              s1 = peg$c28(s2, s3);
              s0 = s1;
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c27); }
      }

      return s0;
    }

    function peg$parsepartial() {
      var s0, s1, s2, s3, s4, s5, s6, s7, s8, s9;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 62) {
          s2 = peg$c30;
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c31); }
        }
        if (s2 === peg$FAILED) {
          if (input.charCodeAt(peg$currPos) === 43) {
            s2 = peg$c32;
            peg$currPos++;
          } else {
            s2 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c33); }
          }
        }
        if (s2 !== peg$FAILED) {
          s3 = [];
          s4 = peg$parsews();
          while (s4 !== peg$FAILED) {
            s3.push(s4);
            s4 = peg$parsews();
          }
          if (s3 !== peg$FAILED) {
            s4 = peg$currPos;
            s5 = peg$parsekey();
            if (s5 !== peg$FAILED) {
              peg$reportedPos = s4;
              s5 = peg$c34(s5);
            }
            s4 = s5;
            if (s4 === peg$FAILED) {
              s4 = peg$parseinline();
            }
            if (s4 !== peg$FAILED) {
              s5 = peg$parsecontext();
              if (s5 !== peg$FAILED) {
                s6 = peg$parseparams();
                if (s6 !== peg$FAILED) {
                  s7 = [];
                  s8 = peg$parsews();
                  while (s8 !== peg$FAILED) {
                    s7.push(s8);
                    s8 = peg$parsews();
                  }
                  if (s7 !== peg$FAILED) {
                    if (input.charCodeAt(peg$currPos) === 47) {
                      s8 = peg$c8;
                      peg$currPos++;
                    } else {
                      s8 = peg$FAILED;
                      if (peg$silentFails === 0) { peg$fail(peg$c9); }
                    }
                    if (s8 !== peg$FAILED) {
                      s9 = peg$parserd();
                      if (s9 !== peg$FAILED) {
                        peg$reportedPos = s0;
                        s1 = peg$c35(s2, s4, s5, s6);
                        s0 = s1;
                      } else {
                        peg$currPos = s0;
                        s0 = peg$c3;
                      }
                    } else {
                      peg$currPos = s0;
                      s0 = peg$c3;
                    }
                  } else {
                    peg$currPos = s0;
                    s0 = peg$c3;
                  }
                } else {
                  peg$currPos = s0;
                  s0 = peg$c3;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c29); }
      }

      return s0;
    }

    function peg$parsefilters() {
      var s0, s1, s2, s3, s4;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      s2 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 124) {
        s3 = peg$c37;
        peg$currPos++;
      } else {
        s3 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c38); }
      }
      if (s3 !== peg$FAILED) {
        s4 = peg$parsekey();
        if (s4 !== peg$FAILED) {
          peg$reportedPos = s2;
          s3 = peg$c18(s4);
          s2 = s3;
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$c3;
      }
      while (s2 !== peg$FAILED) {
        s1.push(s2);
        s2 = peg$currPos;
        if (input.charCodeAt(peg$currPos) === 124) {
          s3 = peg$c37;
          peg$currPos++;
        } else {
          s3 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c38); }
        }
        if (s3 !== peg$FAILED) {
          s4 = peg$parsekey();
          if (s4 !== peg$FAILED) {
            peg$reportedPos = s2;
            s3 = peg$c18(s4);
            s2 = s3;
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c39(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c36); }
      }

      return s0;
    }

    function peg$parsespecial() {
      var s0, s1, s2, s3, s4;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 126) {
          s2 = peg$c41;
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c42); }
        }
        if (s2 !== peg$FAILED) {
          s3 = peg$parsekey();
          if (s3 !== peg$FAILED) {
            s4 = peg$parserd();
            if (s4 !== peg$FAILED) {
              peg$reportedPos = s0;
              s1 = peg$c43(s3);
              s0 = s1;
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c40); }
      }

      return s0;
    }

    function peg$parseidentifier() {
      var s0, s1;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parsepath();
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c45(s1);
      }
      s0 = s1;
      if (s0 === peg$FAILED) {
        s0 = peg$currPos;
        s1 = peg$parsekey();
        if (s1 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c46(s1);
        }
        s0 = s1;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c44); }
      }

      return s0;
    }

    function peg$parsenumber() {
      var s0, s1;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parsefloat();
      if (s1 === peg$FAILED) {
        s1 = peg$parseinteger();
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c48(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c47); }
      }

      return s0;
    }

    function peg$parsefloat() {
      var s0, s1, s2, s3;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseinteger();
      if (s1 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 46) {
          s2 = peg$c50;
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c51); }
        }
        if (s2 !== peg$FAILED) {
          s3 = peg$parseunsigned_integer();
          if (s3 !== peg$FAILED) {
            peg$reportedPos = s0;
            s1 = peg$c52(s1, s3);
            s0 = s1;
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c49); }
      }

      return s0;
    }

    function peg$parseunsigned_integer() {
      var s0, s1, s2;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      if (peg$c54.test(input.charAt(peg$currPos))) {
        s2 = input.charAt(peg$currPos);
        peg$currPos++;
      } else {
        s2 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c55); }
      }
      if (s2 !== peg$FAILED) {
        while (s2 !== peg$FAILED) {
          s1.push(s2);
          if (peg$c54.test(input.charAt(peg$currPos))) {
            s2 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s2 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c55); }
          }
        }
      } else {
        s1 = peg$c3;
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c56(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c53); }
      }

      return s0;
    }

    function peg$parsesigned_integer() {
      var s0, s1, s2;

      peg$silentFails++;
      s0 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 45) {
        s1 = peg$c58;
        peg$currPos++;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c59); }
      }
      if (s1 !== peg$FAILED) {
        s2 = peg$parseunsigned_integer();
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c60(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c57); }
      }

      return s0;
    }

    function peg$parseinteger() {
      var s0, s1;

      peg$silentFails++;
      s0 = peg$parsesigned_integer();
      if (s0 === peg$FAILED) {
        s0 = peg$parseunsigned_integer();
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c61); }
      }

      return s0;
    }

    function peg$parsepath() {
      var s0, s1, s2, s3;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parsekey();
      if (s1 === peg$FAILED) {
        s1 = peg$c4;
      }
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$parsearray_part();
        if (s3 === peg$FAILED) {
          s3 = peg$parsearray();
        }
        if (s3 !== peg$FAILED) {
          while (s3 !== peg$FAILED) {
            s2.push(s3);
            s3 = peg$parsearray_part();
            if (s3 === peg$FAILED) {
              s3 = peg$parsearray();
            }
          }
        } else {
          s2 = peg$c3;
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c63(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      if (s0 === peg$FAILED) {
        s0 = peg$currPos;
        if (input.charCodeAt(peg$currPos) === 46) {
          s1 = peg$c50;
          peg$currPos++;
        } else {
          s1 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c51); }
        }
        if (s1 !== peg$FAILED) {
          s2 = [];
          s3 = peg$parsearray_part();
          if (s3 === peg$FAILED) {
            s3 = peg$parsearray();
          }
          while (s3 !== peg$FAILED) {
            s2.push(s3);
            s3 = peg$parsearray_part();
            if (s3 === peg$FAILED) {
              s3 = peg$parsearray();
            }
          }
          if (s2 !== peg$FAILED) {
            peg$reportedPos = s0;
            s1 = peg$c64(s2);
            s0 = s1;
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c62); }
      }

      return s0;
    }

    function peg$parsekey() {
      var s0, s1, s2, s3;

      peg$silentFails++;
      s0 = peg$currPos;
      if (peg$c66.test(input.charAt(peg$currPos))) {
        s1 = input.charAt(peg$currPos);
        peg$currPos++;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c67); }
      }
      if (s1 !== peg$FAILED) {
        s2 = [];
        if (peg$c68.test(input.charAt(peg$currPos))) {
          s3 = input.charAt(peg$currPos);
          peg$currPos++;
        } else {
          s3 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c69); }
        }
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          if (peg$c68.test(input.charAt(peg$currPos))) {
            s3 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s3 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c69); }
          }
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c70(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c65); }
      }

      return s0;
    }

    function peg$parsearray() {
      var s0, s1, s2, s3, s4, s5;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$currPos;
      s2 = peg$parselb();
      if (s2 !== peg$FAILED) {
        s3 = peg$currPos;
        s4 = [];
        if (peg$c54.test(input.charAt(peg$currPos))) {
          s5 = input.charAt(peg$currPos);
          peg$currPos++;
        } else {
          s5 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c55); }
        }
        if (s5 !== peg$FAILED) {
          while (s5 !== peg$FAILED) {
            s4.push(s5);
            if (peg$c54.test(input.charAt(peg$currPos))) {
              s5 = input.charAt(peg$currPos);
              peg$currPos++;
            } else {
              s5 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c55); }
            }
          }
        } else {
          s4 = peg$c3;
        }
        if (s4 !== peg$FAILED) {
          peg$reportedPos = s3;
          s4 = peg$c72(s4);
        }
        s3 = s4;
        if (s3 === peg$FAILED) {
          s3 = peg$parseidentifier();
        }
        if (s3 !== peg$FAILED) {
          s4 = peg$parserb();
          if (s4 !== peg$FAILED) {
            peg$reportedPos = s1;
            s2 = peg$c73(s3);
            s1 = s2;
          } else {
            peg$currPos = s1;
            s1 = peg$c3;
          }
        } else {
          peg$currPos = s1;
          s1 = peg$c3;
        }
      } else {
        peg$currPos = s1;
        s1 = peg$c3;
      }
      if (s1 !== peg$FAILED) {
        s2 = peg$parsearray_part();
        if (s2 === peg$FAILED) {
          s2 = peg$c4;
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c74(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c71); }
      }

      return s0;
    }

    function peg$parsearray_part() {
      var s0, s1, s2, s3, s4;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      s2 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 46) {
        s3 = peg$c50;
        peg$currPos++;
      } else {
        s3 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c51); }
      }
      if (s3 !== peg$FAILED) {
        s4 = peg$parsekey();
        if (s4 !== peg$FAILED) {
          peg$reportedPos = s2;
          s3 = peg$c76(s4);
          s2 = s3;
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$c3;
      }
      if (s2 !== peg$FAILED) {
        while (s2 !== peg$FAILED) {
          s1.push(s2);
          s2 = peg$currPos;
          if (input.charCodeAt(peg$currPos) === 46) {
            s3 = peg$c50;
            peg$currPos++;
          } else {
            s3 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c51); }
          }
          if (s3 !== peg$FAILED) {
            s4 = peg$parsekey();
            if (s4 !== peg$FAILED) {
              peg$reportedPos = s2;
              s3 = peg$c76(s4);
              s2 = s3;
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        }
      } else {
        s1 = peg$c3;
      }
      if (s1 !== peg$FAILED) {
        s2 = peg$parsearray();
        if (s2 === peg$FAILED) {
          s2 = peg$c4;
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c77(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c75); }
      }

      return s0;
    }

    function peg$parseinline() {
      var s0, s1, s2, s3;

      peg$silentFails++;
      s0 = peg$currPos;
      if (input.charCodeAt(peg$currPos) === 34) {
        s1 = peg$c79;
        peg$currPos++;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c80); }
      }
      if (s1 !== peg$FAILED) {
        if (input.charCodeAt(peg$currPos) === 34) {
          s2 = peg$c79;
          peg$currPos++;
        } else {
          s2 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c80); }
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c81();
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      if (s0 === peg$FAILED) {
        s0 = peg$currPos;
        if (input.charCodeAt(peg$currPos) === 34) {
          s1 = peg$c79;
          peg$currPos++;
        } else {
          s1 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c80); }
        }
        if (s1 !== peg$FAILED) {
          s2 = peg$parseliteral();
          if (s2 !== peg$FAILED) {
            if (input.charCodeAt(peg$currPos) === 34) {
              s3 = peg$c79;
              peg$currPos++;
            } else {
              s3 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c80); }
            }
            if (s3 !== peg$FAILED) {
              peg$reportedPos = s0;
              s1 = peg$c82(s2);
              s0 = s1;
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
        if (s0 === peg$FAILED) {
          s0 = peg$currPos;
          if (input.charCodeAt(peg$currPos) === 34) {
            s1 = peg$c79;
            peg$currPos++;
          } else {
            s1 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c80); }
          }
          if (s1 !== peg$FAILED) {
            s2 = [];
            s3 = peg$parseinline_part();
            if (s3 !== peg$FAILED) {
              while (s3 !== peg$FAILED) {
                s2.push(s3);
                s3 = peg$parseinline_part();
              }
            } else {
              s2 = peg$c3;
            }
            if (s2 !== peg$FAILED) {
              if (input.charCodeAt(peg$currPos) === 34) {
                s3 = peg$c79;
                peg$currPos++;
              } else {
                s3 = peg$FAILED;
                if (peg$silentFails === 0) { peg$fail(peg$c80); }
              }
              if (s3 !== peg$FAILED) {
                peg$reportedPos = s0;
                s1 = peg$c83(s2);
                s0 = s1;
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        }
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c78); }
      }

      return s0;
    }

    function peg$parseinline_part() {
      var s0, s1;

      s0 = peg$parsespecial();
      if (s0 === peg$FAILED) {
        s0 = peg$parsereference();
        if (s0 === peg$FAILED) {
          s0 = peg$currPos;
          s1 = peg$parseliteral();
          if (s1 !== peg$FAILED) {
            peg$reportedPos = s0;
            s1 = peg$c84(s1);
          }
          s0 = s1;
        }
      }

      return s0;
    }

    function peg$parsebuffer() {
      var s0, s1, s2, s3, s4, s5, s6, s7;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = peg$parseeol();
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$parsews();
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$parsews();
        }
        if (s2 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c86(s1, s2);
          s0 = s1;
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      if (s0 === peg$FAILED) {
        s0 = peg$currPos;
        s1 = [];
        s2 = peg$currPos;
        s3 = peg$currPos;
        peg$silentFails++;
        s4 = peg$parsetag();
        peg$silentFails--;
        if (s4 === peg$FAILED) {
          s3 = peg$c6;
        } else {
          peg$currPos = s3;
          s3 = peg$c3;
        }
        if (s3 !== peg$FAILED) {
          s4 = peg$currPos;
          peg$silentFails++;
          s5 = peg$parseraw();
          peg$silentFails--;
          if (s5 === peg$FAILED) {
            s4 = peg$c6;
          } else {
            peg$currPos = s4;
            s4 = peg$c3;
          }
          if (s4 !== peg$FAILED) {
            s5 = peg$currPos;
            peg$silentFails++;
            s6 = peg$parsecomment();
            peg$silentFails--;
            if (s6 === peg$FAILED) {
              s5 = peg$c6;
            } else {
              peg$currPos = s5;
              s5 = peg$c3;
            }
            if (s5 !== peg$FAILED) {
              s6 = peg$currPos;
              peg$silentFails++;
              s7 = peg$parseeol();
              peg$silentFails--;
              if (s7 === peg$FAILED) {
                s6 = peg$c6;
              } else {
                peg$currPos = s6;
                s6 = peg$c3;
              }
              if (s6 !== peg$FAILED) {
                if (input.length > peg$currPos) {
                  s7 = input.charAt(peg$currPos);
                  peg$currPos++;
                } else {
                  s7 = peg$FAILED;
                  if (peg$silentFails === 0) { peg$fail(peg$c87); }
                }
                if (s7 !== peg$FAILED) {
                  peg$reportedPos = s2;
                  s3 = peg$c88(s7);
                  s2 = s3;
                } else {
                  peg$currPos = s2;
                  s2 = peg$c3;
                }
              } else {
                peg$currPos = s2;
                s2 = peg$c3;
              }
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
        if (s2 !== peg$FAILED) {
          while (s2 !== peg$FAILED) {
            s1.push(s2);
            s2 = peg$currPos;
            s3 = peg$currPos;
            peg$silentFails++;
            s4 = peg$parsetag();
            peg$silentFails--;
            if (s4 === peg$FAILED) {
              s3 = peg$c6;
            } else {
              peg$currPos = s3;
              s3 = peg$c3;
            }
            if (s3 !== peg$FAILED) {
              s4 = peg$currPos;
              peg$silentFails++;
              s5 = peg$parseraw();
              peg$silentFails--;
              if (s5 === peg$FAILED) {
                s4 = peg$c6;
              } else {
                peg$currPos = s4;
                s4 = peg$c3;
              }
              if (s4 !== peg$FAILED) {
                s5 = peg$currPos;
                peg$silentFails++;
                s6 = peg$parsecomment();
                peg$silentFails--;
                if (s6 === peg$FAILED) {
                  s5 = peg$c6;
                } else {
                  peg$currPos = s5;
                  s5 = peg$c3;
                }
                if (s5 !== peg$FAILED) {
                  s6 = peg$currPos;
                  peg$silentFails++;
                  s7 = peg$parseeol();
                  peg$silentFails--;
                  if (s7 === peg$FAILED) {
                    s6 = peg$c6;
                  } else {
                    peg$currPos = s6;
                    s6 = peg$c3;
                  }
                  if (s6 !== peg$FAILED) {
                    if (input.length > peg$currPos) {
                      s7 = input.charAt(peg$currPos);
                      peg$currPos++;
                    } else {
                      s7 = peg$FAILED;
                      if (peg$silentFails === 0) { peg$fail(peg$c87); }
                    }
                    if (s7 !== peg$FAILED) {
                      peg$reportedPos = s2;
                      s3 = peg$c88(s7);
                      s2 = s3;
                    } else {
                      peg$currPos = s2;
                      s2 = peg$c3;
                    }
                  } else {
                    peg$currPos = s2;
                    s2 = peg$c3;
                  }
                } else {
                  peg$currPos = s2;
                  s2 = peg$c3;
                }
              } else {
                peg$currPos = s2;
                s2 = peg$c3;
              }
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          }
        } else {
          s1 = peg$c3;
        }
        if (s1 !== peg$FAILED) {
          peg$reportedPos = s0;
          s1 = peg$c89(s1);
        }
        s0 = s1;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c85); }
      }

      return s0;
    }

    function peg$parseliteral() {
      var s0, s1, s2, s3, s4;

      peg$silentFails++;
      s0 = peg$currPos;
      s1 = [];
      s2 = peg$currPos;
      s3 = peg$currPos;
      peg$silentFails++;
      s4 = peg$parsetag();
      peg$silentFails--;
      if (s4 === peg$FAILED) {
        s3 = peg$c6;
      } else {
        peg$currPos = s3;
        s3 = peg$c3;
      }
      if (s3 !== peg$FAILED) {
        s4 = peg$parseesc();
        if (s4 === peg$FAILED) {
          if (peg$c91.test(input.charAt(peg$currPos))) {
            s4 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s4 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c92); }
          }
        }
        if (s4 !== peg$FAILED) {
          peg$reportedPos = s2;
          s3 = peg$c88(s4);
          s2 = s3;
        } else {
          peg$currPos = s2;
          s2 = peg$c3;
        }
      } else {
        peg$currPos = s2;
        s2 = peg$c3;
      }
      if (s2 !== peg$FAILED) {
        while (s2 !== peg$FAILED) {
          s1.push(s2);
          s2 = peg$currPos;
          s3 = peg$currPos;
          peg$silentFails++;
          s4 = peg$parsetag();
          peg$silentFails--;
          if (s4 === peg$FAILED) {
            s3 = peg$c6;
          } else {
            peg$currPos = s3;
            s3 = peg$c3;
          }
          if (s3 !== peg$FAILED) {
            s4 = peg$parseesc();
            if (s4 === peg$FAILED) {
              if (peg$c91.test(input.charAt(peg$currPos))) {
                s4 = input.charAt(peg$currPos);
                peg$currPos++;
              } else {
                s4 = peg$FAILED;
                if (peg$silentFails === 0) { peg$fail(peg$c92); }
              }
            }
            if (s4 !== peg$FAILED) {
              peg$reportedPos = s2;
              s3 = peg$c88(s4);
              s2 = s3;
            } else {
              peg$currPos = s2;
              s2 = peg$c3;
            }
          } else {
            peg$currPos = s2;
            s2 = peg$c3;
          }
        }
      } else {
        s1 = peg$c3;
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c93(s1);
      }
      s0 = s1;
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c90); }
      }

      return s0;
    }

    function peg$parseesc() {
      var s0, s1;

      s0 = peg$currPos;
      if (input.substr(peg$currPos, 2) === peg$c94) {
        s1 = peg$c94;
        peg$currPos += 2;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c95); }
      }
      if (s1 !== peg$FAILED) {
        peg$reportedPos = s0;
        s1 = peg$c96();
      }
      s0 = s1;

      return s0;
    }

    function peg$parseraw() {
      var s0, s1, s2, s3, s4, s5;

      peg$silentFails++;
      s0 = peg$currPos;
      if (input.substr(peg$currPos, 2) === peg$c98) {
        s1 = peg$c98;
        peg$currPos += 2;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c99); }
      }
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$currPos;
        s4 = peg$currPos;
        peg$silentFails++;
        if (input.substr(peg$currPos, 2) === peg$c100) {
          s5 = peg$c100;
          peg$currPos += 2;
        } else {
          s5 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c101); }
        }
        peg$silentFails--;
        if (s5 === peg$FAILED) {
          s4 = peg$c6;
        } else {
          peg$currPos = s4;
          s4 = peg$c3;
        }
        if (s4 !== peg$FAILED) {
          if (input.length > peg$currPos) {
            s5 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s5 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c87); }
          }
          if (s5 !== peg$FAILED) {
            peg$reportedPos = s3;
            s4 = peg$c102(s5);
            s3 = s4;
          } else {
            peg$currPos = s3;
            s3 = peg$c3;
          }
        } else {
          peg$currPos = s3;
          s3 = peg$c3;
        }
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$currPos;
          s4 = peg$currPos;
          peg$silentFails++;
          if (input.substr(peg$currPos, 2) === peg$c100) {
            s5 = peg$c100;
            peg$currPos += 2;
          } else {
            s5 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c101); }
          }
          peg$silentFails--;
          if (s5 === peg$FAILED) {
            s4 = peg$c6;
          } else {
            peg$currPos = s4;
            s4 = peg$c3;
          }
          if (s4 !== peg$FAILED) {
            if (input.length > peg$currPos) {
              s5 = input.charAt(peg$currPos);
              peg$currPos++;
            } else {
              s5 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c87); }
            }
            if (s5 !== peg$FAILED) {
              peg$reportedPos = s3;
              s4 = peg$c102(s5);
              s3 = s4;
            } else {
              peg$currPos = s3;
              s3 = peg$c3;
            }
          } else {
            peg$currPos = s3;
            s3 = peg$c3;
          }
        }
        if (s2 !== peg$FAILED) {
          if (input.substr(peg$currPos, 2) === peg$c100) {
            s3 = peg$c100;
            peg$currPos += 2;
          } else {
            s3 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c101); }
          }
          if (s3 !== peg$FAILED) {
            peg$reportedPos = s0;
            s1 = peg$c103(s2);
            s0 = s1;
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c97); }
      }

      return s0;
    }

    function peg$parsecomment() {
      var s0, s1, s2, s3, s4, s5;

      peg$silentFails++;
      s0 = peg$currPos;
      if (input.substr(peg$currPos, 2) === peg$c105) {
        s1 = peg$c105;
        peg$currPos += 2;
      } else {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c106); }
      }
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$currPos;
        s4 = peg$currPos;
        peg$silentFails++;
        if (input.substr(peg$currPos, 2) === peg$c107) {
          s5 = peg$c107;
          peg$currPos += 2;
        } else {
          s5 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c108); }
        }
        peg$silentFails--;
        if (s5 === peg$FAILED) {
          s4 = peg$c6;
        } else {
          peg$currPos = s4;
          s4 = peg$c3;
        }
        if (s4 !== peg$FAILED) {
          if (input.length > peg$currPos) {
            s5 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s5 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c87); }
          }
          if (s5 !== peg$FAILED) {
            peg$reportedPos = s3;
            s4 = peg$c88(s5);
            s3 = s4;
          } else {
            peg$currPos = s3;
            s3 = peg$c3;
          }
        } else {
          peg$currPos = s3;
          s3 = peg$c3;
        }
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$currPos;
          s4 = peg$currPos;
          peg$silentFails++;
          if (input.substr(peg$currPos, 2) === peg$c107) {
            s5 = peg$c107;
            peg$currPos += 2;
          } else {
            s5 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c108); }
          }
          peg$silentFails--;
          if (s5 === peg$FAILED) {
            s4 = peg$c6;
          } else {
            peg$currPos = s4;
            s4 = peg$c3;
          }
          if (s4 !== peg$FAILED) {
            if (input.length > peg$currPos) {
              s5 = input.charAt(peg$currPos);
              peg$currPos++;
            } else {
              s5 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c87); }
            }
            if (s5 !== peg$FAILED) {
              peg$reportedPos = s3;
              s4 = peg$c88(s5);
              s3 = s4;
            } else {
              peg$currPos = s3;
              s3 = peg$c3;
            }
          } else {
            peg$currPos = s3;
            s3 = peg$c3;
          }
        }
        if (s2 !== peg$FAILED) {
          if (input.substr(peg$currPos, 2) === peg$c107) {
            s3 = peg$c107;
            peg$currPos += 2;
          } else {
            s3 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c108); }
          }
          if (s3 !== peg$FAILED) {
            peg$reportedPos = s0;
            s1 = peg$c109(s2);
            s0 = s1;
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      peg$silentFails--;
      if (s0 === peg$FAILED) {
        s1 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c104); }
      }

      return s0;
    }

    function peg$parsetag() {
      var s0, s1, s2, s3, s4, s5, s6, s7, s8, s9;

      s0 = peg$currPos;
      s1 = peg$parseld();
      if (s1 !== peg$FAILED) {
        s2 = [];
        s3 = peg$parsews();
        while (s3 !== peg$FAILED) {
          s2.push(s3);
          s3 = peg$parsews();
        }
        if (s2 !== peg$FAILED) {
          if (peg$c110.test(input.charAt(peg$currPos))) {
            s3 = input.charAt(peg$currPos);
            peg$currPos++;
          } else {
            s3 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c111); }
          }
          if (s3 !== peg$FAILED) {
            s4 = [];
            s5 = peg$parsews();
            while (s5 !== peg$FAILED) {
              s4.push(s5);
              s5 = peg$parsews();
            }
            if (s4 !== peg$FAILED) {
              s5 = [];
              s6 = peg$currPos;
              s7 = peg$currPos;
              peg$silentFails++;
              s8 = peg$parserd();
              peg$silentFails--;
              if (s8 === peg$FAILED) {
                s7 = peg$c6;
              } else {
                peg$currPos = s7;
                s7 = peg$c3;
              }
              if (s7 !== peg$FAILED) {
                s8 = peg$currPos;
                peg$silentFails++;
                s9 = peg$parseeol();
                peg$silentFails--;
                if (s9 === peg$FAILED) {
                  s8 = peg$c6;
                } else {
                  peg$currPos = s8;
                  s8 = peg$c3;
                }
                if (s8 !== peg$FAILED) {
                  if (input.length > peg$currPos) {
                    s9 = input.charAt(peg$currPos);
                    peg$currPos++;
                  } else {
                    s9 = peg$FAILED;
                    if (peg$silentFails === 0) { peg$fail(peg$c87); }
                  }
                  if (s9 !== peg$FAILED) {
                    s7 = [s7, s8, s9];
                    s6 = s7;
                  } else {
                    peg$currPos = s6;
                    s6 = peg$c3;
                  }
                } else {
                  peg$currPos = s6;
                  s6 = peg$c3;
                }
              } else {
                peg$currPos = s6;
                s6 = peg$c3;
              }
              if (s6 !== peg$FAILED) {
                while (s6 !== peg$FAILED) {
                  s5.push(s6);
                  s6 = peg$currPos;
                  s7 = peg$currPos;
                  peg$silentFails++;
                  s8 = peg$parserd();
                  peg$silentFails--;
                  if (s8 === peg$FAILED) {
                    s7 = peg$c6;
                  } else {
                    peg$currPos = s7;
                    s7 = peg$c3;
                  }
                  if (s7 !== peg$FAILED) {
                    s8 = peg$currPos;
                    peg$silentFails++;
                    s9 = peg$parseeol();
                    peg$silentFails--;
                    if (s9 === peg$FAILED) {
                      s8 = peg$c6;
                    } else {
                      peg$currPos = s8;
                      s8 = peg$c3;
                    }
                    if (s8 !== peg$FAILED) {
                      if (input.length > peg$currPos) {
                        s9 = input.charAt(peg$currPos);
                        peg$currPos++;
                      } else {
                        s9 = peg$FAILED;
                        if (peg$silentFails === 0) { peg$fail(peg$c87); }
                      }
                      if (s9 !== peg$FAILED) {
                        s7 = [s7, s8, s9];
                        s6 = s7;
                      } else {
                        peg$currPos = s6;
                        s6 = peg$c3;
                      }
                    } else {
                      peg$currPos = s6;
                      s6 = peg$c3;
                    }
                  } else {
                    peg$currPos = s6;
                    s6 = peg$c3;
                  }
                }
              } else {
                s5 = peg$c3;
              }
              if (s5 !== peg$FAILED) {
                s6 = [];
                s7 = peg$parsews();
                while (s7 !== peg$FAILED) {
                  s6.push(s7);
                  s7 = peg$parsews();
                }
                if (s6 !== peg$FAILED) {
                  s7 = peg$parserd();
                  if (s7 !== peg$FAILED) {
                    s1 = [s1, s2, s3, s4, s5, s6, s7];
                    s0 = s1;
                  } else {
                    peg$currPos = s0;
                    s0 = peg$c3;
                  }
                } else {
                  peg$currPos = s0;
                  s0 = peg$c3;
                }
              } else {
                peg$currPos = s0;
                s0 = peg$c3;
              }
            } else {
              peg$currPos = s0;
              s0 = peg$c3;
            }
          } else {
            peg$currPos = s0;
            s0 = peg$c3;
          }
        } else {
          peg$currPos = s0;
          s0 = peg$c3;
        }
      } else {
        peg$currPos = s0;
        s0 = peg$c3;
      }
      if (s0 === peg$FAILED) {
        s0 = peg$parsereference();
      }

      return s0;
    }

    function peg$parseld() {
      var s0;

      if (input.charCodeAt(peg$currPos) === 123) {
        s0 = peg$c112;
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c113); }
      }

      return s0;
    }

    function peg$parserd() {
      var s0;

      if (input.charCodeAt(peg$currPos) === 125) {
        s0 = peg$c114;
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c115); }
      }

      return s0;
    }

    function peg$parselb() {
      var s0;

      if (input.charCodeAt(peg$currPos) === 91) {
        s0 = peg$c116;
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c117); }
      }

      return s0;
    }

    function peg$parserb() {
      var s0;

      if (input.charCodeAt(peg$currPos) === 93) {
        s0 = peg$c118;
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c119); }
      }

      return s0;
    }

    function peg$parseeol() {
      var s0;

      if (input.charCodeAt(peg$currPos) === 10) {
        s0 = peg$c120;
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c121); }
      }
      if (s0 === peg$FAILED) {
        if (input.substr(peg$currPos, 2) === peg$c122) {
          s0 = peg$c122;
          peg$currPos += 2;
        } else {
          s0 = peg$FAILED;
          if (peg$silentFails === 0) { peg$fail(peg$c123); }
        }
        if (s0 === peg$FAILED) {
          if (input.charCodeAt(peg$currPos) === 13) {
            s0 = peg$c124;
            peg$currPos++;
          } else {
            s0 = peg$FAILED;
            if (peg$silentFails === 0) { peg$fail(peg$c125); }
          }
          if (s0 === peg$FAILED) {
            if (input.charCodeAt(peg$currPos) === 8232) {
              s0 = peg$c126;
              peg$currPos++;
            } else {
              s0 = peg$FAILED;
              if (peg$silentFails === 0) { peg$fail(peg$c127); }
            }
            if (s0 === peg$FAILED) {
              if (input.charCodeAt(peg$currPos) === 8233) {
                s0 = peg$c128;
                peg$currPos++;
              } else {
                s0 = peg$FAILED;
                if (peg$silentFails === 0) { peg$fail(peg$c129); }
              }
            }
          }
        }
      }

      return s0;
    }

    function peg$parsews() {
      var s0;

      if (peg$c130.test(input.charAt(peg$currPos))) {
        s0 = input.charAt(peg$currPos);
        peg$currPos++;
      } else {
        s0 = peg$FAILED;
        if (peg$silentFails === 0) { peg$fail(peg$c131); }
      }
      if (s0 === peg$FAILED) {
        s0 = peg$parseeol();
      }

      return s0;
    }


      function makeInteger(arr) {
        return parseInt(arr.join(''), 10);
      }
      function withPosition(arr) {
        return arr.concat([['line', line()], ['col', column()]]);
      }


    peg$result = peg$startRuleFunction();

    if (peg$result !== peg$FAILED && peg$currPos === input.length) {
      return peg$result;
    } else {
      if (peg$result !== peg$FAILED && peg$currPos < input.length) {
        peg$fail({ type: "end", description: "end of input" });
      }

      throw peg$buildException(null, peg$maxFailExpected, peg$maxFailPos);
    }
  }

  return {
    SyntaxError: SyntaxError,
    parse:       parse
  };
})();

  // expose parser methods
  dust.parse = parser.parse;

  return parser;
}));

(function(root, factory) {
  if (typeof define === "function" && define.amd && define.amd.dust === true) {
    define("dust.compile", ["dust.core", "dust.parse"], function(dust, parse) {
      return factory(parse, dust).compile;
    });
  } else if (typeof exports === 'object') {
    // in Node, require this file if we want to use the compiler as a standalone module
    module.exports = factory(require('./parser').parse, require('./dust'));
  } else {
    // in the browser, store the factory output if we want to use the compiler directly
    factory(root.dust.parse, root.dust);
  }
}(this, function(parse, dust) {
  var compiler = {},
      isArray = dust.isArray;


  compiler.compile = function(source, name) {
    // the name parameter is optional.
    // this can happen for templates that are rendered immediately (renderSource which calls compileFn) or
    // for templates that are compiled as a callable (compileFn)
    //
    // for the common case (using compile and render) a name is required so that templates will be cached by name and rendered later, by name.

    try {
      var ast = filterAST(parse(source));
      return compile(ast, name);
    }
    catch (err)
    {
      if (!err.line || !err.column) {
        throw err;
      }
      throw new SyntaxError(err.message + ' At line : ' + err.line + ', column : ' + err.column);
    }
  };

  function filterAST(ast) {
    var context = {};
    return compiler.filterNode(context, ast);
  }

  compiler.filterNode = function(context, node) {
    return compiler.optimizers[node[0]](context, node);
  };

  compiler.optimizers = {
    body:      compactBuffers,
    buffer:    noop,
    special:   convertSpecial,
    format:    format,
    reference: visit,
    '#':       visit,
    '?':       visit,
    '^':       visit,
    '<':       visit,
    '+':       visit,
    '@':       visit,
    '%':       visit,
    partial:   visit,
    context:   visit,
    params:    visit,
    bodies:    visit,
    param:     visit,
    filters:   noop,
    key:       noop,
    path:      noop,
    literal:   noop,
    raw:       noop,
    comment:   nullify,
    line:      nullify,
    col:       nullify
  };

  compiler.pragmas = {
    esc: function(compiler, context, bodies) {
      var old = compiler.auto,
          out;
      if (!context) {
        context = 'h';
      }
      compiler.auto = (context === 's') ? '' : context;
      out = compileParts(compiler, bodies.block);
      compiler.auto = old;
      return out;
    }
  };

  function visit(context, node) {
    var out = [node[0]],
        i, len, res;
    for (i=1, len=node.length; i<len; i++) {
      res = compiler.filterNode(context, node[i]);
      if (res) {
        out.push(res);
      }
    }
    return out;
  }

  // Compacts consecutive buffer nodes into a single node
  function compactBuffers(context, node) {
    var out = [node[0]],
        memo, i, len, res;
    for (i=1, len=node.length; i<len; i++) {
      res = compiler.filterNode(context, node[i]);
      if (res) {
        if (res[0] === 'buffer' || res[0] === 'format') {
          if (memo) {
            memo[0] = (res[0] === 'buffer') ? 'buffer' : memo[0];
            memo[1] += res.slice(1, -2).join('');
          } else {
            memo = res;
            out.push(res);
          }
        } else {
          memo = null;
          out.push(res);
        }
      }
    }
    return out;
  }

  var specialChars = {
    's': ' ',
    'n': '\n',
    'r': '\r',
    'lb': '{',
    'rb': '}'
  };

  function convertSpecial(context, node) {
    return ['buffer', specialChars[node[1]], node[2], node[3]];
  }

  function noop(context, node) {
    return node;
  }

  function nullify(){}

  function format(context, node) {
    if(dust.config.whitespace) {
      // Format nodes are in the form ['format', eol, whitespace, line, col],
      // which is unlike other nodes in that there are two pieces of content
      // Join eol and whitespace together to normalize the node format
      node.splice(1, 2, node.slice(1, -2).join(''));
      return node;
    }
    return null;
  }

  function compile(ast, name) {
    var context = {
      name: name,
      bodies: [],
      blocks: {},
      index: 0,
      auto: 'h'
    },
    escapedName = dust.escapeJs(name),
    AMDName = name? '"' + escapedName + '",' : '',
    compiled = 'function(dust){',
    entry = compiler.compileNode(context, ast),
    iife;

    if(name) {
      compiled += 'dust.register("' + escapedName + '",' + entry + ');';
    }

    compiled += compileBlocks(context) +
                compileBodies(context) +
                'return ' + entry + '}';

    iife = '(' + compiled + '(dust));';

    if(dust.config.amd) {
      return 'define(' + AMDName + '["dust.core"],' + compiled + ');';
    } else if(dust.config.cjs) {
      return 'module.exports=function(dust){' +
             'var tmpl=' + iife +
             'var f=' + loaderFor().toString() + ';' +
             'f.template=tmpl;return f}';
    } else {
      return iife;
    }
  }

  function compileBlocks(context) {
    var out = [],
        blocks = context.blocks,
        name;

    for (name in blocks) {
      out.push('"' + name + '":' + blocks[name]);
    }
    if (out.length) {
      context.blocks = 'ctx=ctx.shiftBlocks(blocks);';
      return 'var blocks={' + out.join(',') + '};';
    } else {
      context.blocks = '';
    }
    return context.blocks;
  }

  function compileBodies(context) {
    var out = [],
        bodies = context.bodies,
        blx = context.blocks,
        i, len;

    for (i=0, len=bodies.length; i<len; i++) {
      out[i] = 'function body_' + i + '(chk,ctx){' +
          blx + 'return chk' + bodies[i] + ';}body_' + i + '.__dustBody=!0;';
    }
    return out.join('');
  }

  function compileParts(context, body) {
    var parts = '',
        i, len;
    for (i=1, len=body.length; i<len; i++) {
      parts += compiler.compileNode(context, body[i]);
    }
    return parts;
  }

  compiler.compileNode = function(context, node) {
    return compiler.nodes[node[0]](context, node);
  };

  compiler.nodes = {
    body: function(context, node) {
      var id = context.index++,
          name = 'body_' + id;
      context.bodies[id] = compileParts(context, node);
      return name;
    },

    buffer: function(context, node) {
      return '.w(' + escape(node[1]) + ')';
    },

    format: function(context, node) {
      return '.w(' + escape(node[1]) + ')';
    },

    reference: function(context, node) {
      return '.f(' + compiler.compileNode(context, node[1]) +
        ',ctx,' + compiler.compileNode(context, node[2]) + ')';
    },

    '#': function(context, node) {
      return compileSection(context, node, 'section');
    },

    '?': function(context, node) {
      return compileSection(context, node, 'exists');
    },

    '^': function(context, node) {
      return compileSection(context, node, 'notexists');
    },

    '<': function(context, node) {
      var bodies = node[4];
      for (var i=1, len=bodies.length; i<len; i++) {
        var param = bodies[i],
            type = param[1][1];
        if (type === 'block') {
          context.blocks[node[1].text] = compiler.compileNode(context, param[2]);
          return '';
        }
      }
      return '';
    },

    '+': function(context, node) {
      if (typeof(node[1].text) === 'undefined'  && typeof(node[4]) === 'undefined'){
        return '.b(ctx.getBlock(' +
              compiler.compileNode(context, node[1]) +
              ',chk, ctx),' + compiler.compileNode(context, node[2]) + ', {},' +
              compiler.compileNode(context, node[3]) +
              ')';
      } else {
        return '.b(ctx.getBlock(' +
            escape(node[1].text) +
            '),' + compiler.compileNode(context, node[2]) + ',' +
            compiler.compileNode(context, node[4]) + ',' +
            compiler.compileNode(context, node[3]) +
            ')';
      }
    },

    '@': function(context, node) {
      return '.h(' +
        escape(node[1].text) +
        ',' + compiler.compileNode(context, node[2]) + ',' +
        compiler.compileNode(context, node[4]) + ',' +
        compiler.compileNode(context, node[3]) + ',' +
        compiler.compileNode(context, node[5]) +
        ')';
    },

    '%': function(context, node) {
      // TODO: Move these hacks into pragma precompiler
      var name = node[1][1],
          rawBodies,
          bodies,
          rawParams,
          params,
          ctx, b, p, i, len;
      if (!compiler.pragmas[name]) {
        return '';
      }

      rawBodies = node[4];
      bodies = {};
      for (i=1, len=rawBodies.length; i<len; i++) {
        b = rawBodies[i];
        bodies[b[1][1]] = b[2];
      }

      rawParams = node[3];
      params = {};
      for (i=1, len=rawParams.length; i<len; i++) {
        p = rawParams[i];
        params[p[1][1]] = p[2][1];
      }

      ctx = node[2][1] ? node[2][1].text : null;

      return compiler.pragmas[name](context, ctx, bodies, params);
    },

    partial: function(context, node) {
      return '.p(' +
          compiler.compileNode(context, node[1]) +
          ',ctx,' + compiler.compileNode(context, node[2]) +
          ',' + compiler.compileNode(context, node[3]) + ')';
    },

    context: function(context, node) {
      if (node[1]) {
        return 'ctx.rebase(' + compiler.compileNode(context, node[1]) + ')';
      }
      return 'ctx';
    },

    params: function(context, node) {
      var out = [];
      for (var i=1, len=node.length; i<len; i++) {
        out.push(compiler.compileNode(context, node[i]));
      }
      if (out.length) {
        return '{' + out.join(',') + '}';
      }
      return '{}';
    },

    bodies: function(context, node) {
      var out = [];
      for (var i=1, len=node.length; i<len; i++) {
        out.push(compiler.compileNode(context, node[i]));
      }
      return '{' + out.join(',') + '}';
    },

    param: function(context, node) {
      return compiler.compileNode(context, node[1]) + ':' + compiler.compileNode(context, node[2]);
    },

    filters: function(context, node) {
      var list = [];
      for (var i=1, len=node.length; i<len; i++) {
        var filter = node[i];
        list.push('"' + filter + '"');
      }
      return '"' + context.auto + '"' +
        (list.length ? ',[' + list.join(',') + ']' : '');
    },

    key: function(context, node) {
      return 'ctx.get(["' + node[1] + '"], false)';
    },

    path: function(context, node) {
      var current = node[1],
          keys = node[2],
          list = [];

      for (var i=0,len=keys.length; i<len; i++) {
        if (isArray(keys[i])) {
          list.push(compiler.compileNode(context, keys[i]));
        } else {
          list.push('"' + keys[i] + '"');
        }
      }
      return 'ctx.getPath(' + current + ', [' + list.join(',') + '])';
    },

    literal: function(context, node) {
      return escape(node[1]);
    },
    raw: function(context, node) {
      return ".w(" + escape(node[1]) + ")";
    }
  };

  function compileSection(context, node, cmd) {
    return '.' + (dust._aliases[cmd] || cmd) + '(' +
      compiler.compileNode(context, node[1]) +
      ',' + compiler.compileNode(context, node[2]) + ',' +
      compiler.compileNode(context, node[4]) + ',' +
      compiler.compileNode(context, node[3]) +
      ')';
  }

  var BS = /\\/g,
      DQ = /"/g,
      LF = /\f/g,
      NL = /\n/g,
      CR = /\r/g,
      TB = /\t/g;
  function escapeToJsSafeString(str) {
    return str.replace(BS, '\\\\')
              .replace(DQ, '\\"')
              .replace(LF, '\\f')
              .replace(NL, '\\n')
              .replace(CR, '\\r')
              .replace(TB, '\\t');
  }

  var escape = (typeof JSON === 'undefined') ?
                  function(str) { return '"' + escapeToJsSafeString(str) + '"';} :
                  JSON.stringify;

  function renderSource(source, context, callback) {
    var tmpl = dust.loadSource(dust.compile(source));
    return loaderFor(tmpl)(context, callback);
  }

  function compileFn(source, name) {
    var tmpl = dust.loadSource(dust.compile(source, name));
    return loaderFor(tmpl);
  }

  function loaderFor(tmpl) {
    return function load(ctx, cb) {
      var fn = cb ? 'render' : 'stream';
      return dust[fn](tmpl, ctx, cb);
    };
  }

  // expose compiler methods
  dust.compiler = compiler;
  dust.compile = dust.compiler.compile;
  dust.renderSource = renderSource;
  dust.compileFn = compileFn;

  // DEPRECATED legacy names. Removed in 2.8.0
  dust.filterNode = compiler.filterNode;
  dust.optimizers = compiler.optimizers;
  dust.pragmas = compiler.pragmas;
  dust.compileNode = compiler.compileNode;
  dust.nodes = compiler.nodes;

  return compiler;

}));

if (typeof define === "function" && define.amd && define.amd.dust === true) {
    define(["require", "dust.core", "dust.compile"], function(require, dust) {
        dust.onLoad = function(name, cb) {
            require([name], function() {
                cb();
            });
        };
        return dust;
    });
}
;
/*
ExtendJS 0.2.3
More info at http://extendjs.org

Copyright (c) 2013+ ChrisBenjaminsen.com

Distributed under the terms of the MIT license.
http://extendjs.org/licence.txt

This notice shall be included in all copies or substantial portions of the Software.
*/
!function(a){"use strict";function b(a){a.parent instanceof Function&&(b.apply(this,[a.parent]),this.super=c(this,d(this,this.constructor))),a.apply(this,arguments)}function c(a,b){for(var c in a)"super"!==c&&a[c]instanceof Function&&!(a[c].prototype instanceof Class)&&(b[c]=a[c].super||d(a,a[c]));return b}function d(a,b){var c=a.super;return b.super=function(){return a.super=c,b.apply(a,arguments)}}a.Class=function(){},a.Class.extend=function e(a){function d(){b!==arguments[0]&&(b.apply(this,[a]),c(this,this),this.initializer instanceof Function&&this.initializer.apply(this),this.constructor.apply(this,arguments))}return d.prototype=new this(b),d.prototype.constructor=d,d.toString=function(){return a.toString()},d.extend=function(b){return b.parent=a,e.apply(d,arguments)},d},a.Class=a.Class.extend(function(){this.constructor=function(){}})}(this);
;
// setOps.js MIT License © 2014 James Abney http://github.com/jabney

// Set operations union, intersection, symmetric difference,
// relative complement, equals. Set operations are fast.
(function(so) {
'use strict';
  
  var uidList = [], uid;

  // Create and push the uid identity method.
  uidList.push(uid = function() {
    return this;
  });

  // Push a new uid method onto the stack. Call this and
  // supply a unique key generator for sets of objects.
  so.pushUid = function(method) {
    uidList.push(method);
    uid = method;
    return method;
  };

  // Pop the previously pushed uid method off the stack and
  // assign top of stack to uid. Return the previous method.
  so.popUid = function() {
    var prev;
    uidList.length > 1 && (prev = uidList.pop());
    uid = uidList[uidList.length-1];
    return prev || null;
  };

  // Processes a histogram consructed from two arrays, 'a' and 'b'.
  // This function is used generically by the below set operation 
  // methods, a.k.a, 'evaluators', to return some subset of
  // a set union, based on frequencies in the histogram. 
  function process(a, b, evaluator) {
    // Create a histogram of 'a'.
    var hist = Object.create(null), out = [], ukey, k;
    a.forEach(function(value) {
      ukey = uid.call(value);
      if(!hist[ukey]) {
        hist[ukey] = { value: value, freq: 1 };
      }
    });
    // Merge 'b' into the histogram.
    b.forEach(function(value) {
      ukey = uid.call(value);
      if (hist[ukey]) {
        if (hist[ukey].freq === 1)
          hist[ukey].freq = 3;
      } else {
        hist[ukey] = { value: value, freq: 2 };
      }
    });
    // Call the given evaluator.
    if (evaluator) {
      for (k in hist) {
        if (evaluator(hist[k].freq)) out.push(hist[k].value);
      }
      return out;
    } else {
      return hist;
    }
  };

  // Join two sets together.
  // Set.union([1, 2, 2], [2, 3]) => [1, 2, 3]
  so.union = function(a, b) {
    return process(a, b, function(freq) {
      return true;
    });
  };

  // Return items common to both sets. 
  // Set.intersection([1, 1, 2], [2, 2, 3]) => [2]
  so.intersection = function(a, b) {
    return process(a, b, function(freq) {
      return freq === 3;
    });
  };

  // Symmetric difference. Items from either set that
  // are not in both sets.
  // Set.difference([1, 1, 2], [2, 3, 3]) => [1, 3]
  so.difference = function(a, b) {
    return process(a, b, function(freq) {
      return freq < 3;
    });
  };

  // Relative complement. Items from 'a' which are
  // not also in 'b'.
  // Set.complement([1, 2, 2], [2, 2, 3]) => [3]
  so.complement = function(a, b) {
    return process(a, b, function(freq) {
      return freq === 1;
    });
  };

  // Returns true if both sets are equivalent, false otherwise.
  // Set.equals([1, 1, 2], [1, 2, 2]) => true
  // Set.equals([1, 1, 2], [1, 2, 3]) => false
  so.equals = function(a, b) {
    var max = 0, min = Math.pow(2, 53), key,
      hist = process(a, b);
    for (var key in hist) {
      max = Math.max(max, hist[key].freq);
      min = Math.min(min, hist[key].freq);
    }
    return min === 3 && max === 3;
  };
})(window.setOps = window.setOps || Object.create(null));
;
var LNBase = Class.extend(function(){
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNBase';

	self.options = null;
	self.defaultOptions = {
		rerenderWhenUpdate: false
	};

	self.state = {
	};

	self.items = {};
	self.$item = null;

	self.data = {};

	self.ids = [];
	self.matchedIds = [];
	self.shownIds = [];
	self.showingIds = [];

	self.startIdx = 1;
	self.endIdx = 0;
	self.page = 1;
	self.itemPerPage = 12;

	self.rendered = false;

	self.sortField = '';
	self.sortDir = 'asc';

	self.qs = {};

	self.timeout = [];

	self.constructor = function (options) {
		self.options = $.extend({}, self.defaultOptions, options);
	}


	self.render = function (callback) {
		self.beforeRender();

		var e = $.Event('beforeRender');
		e.lntype = self.type;
		e.lntarget = self;
		$(document).trigger(e);

		dust.render(self.options.template, self, function(err, out) {
			var $el = $(out);
			if ($el.length != 1) $el = $('<div>').html(out);
			$el.addClass('ln-element').addClass(self.options.class).data('lnid', self.getId());

			if (self.$item) {
				self.$item.remove();
				self.$item = null;
			}

			if (callback) {
				self.$item = callback($el);
			} else {
				self.$item = $el.appendTo($(self.options.container).empty());
			}
			if (self.options.itemWrapper) {
				self.$wrapper = self.$item.is(self.options.itemWrapper) ? self.$item : self.$item.find(self.options.itemWrapper);
			}

			// find state fields
			self.$stateFields = self.$item.find('[data-lnstate]');

			self.renderItems();

			// Render state value
			self.renderStateFields ();

			self.afterRender();
			self.rendered = true;

			var e = $.Event('afterRender');
			e.lntype = self.type;
			e.lntarget = self;
			self.$item.trigger(e);
		});
		var e = $.Event('completeRender');
		e.lntype = self.type;
		e.lntarget = self;
		self.$item.trigger(e);
	}

	self.renderStateFields = function () {
		if (self.$stateFields.length) {
			self.$stateFields.each(function () {
				var $field = $(this),
					field = $field.data('lnstate'),
					value = self.getField (field);
				$field.html(value);
			})
		}
	}

	self.beforeRender = function () {
		if (self.options.beforeRender) self.options.beforeRender.apply(self);
	}

	self.afterRender = function () {
		if (self.options.afterRender) self.options.afterRender.apply(self);	
	}

	self.beforeUpdateRender = function () {
		if (self.options.beforeUpdateRender) self.options.beforeUpdateRender.apply(self);
	}

	self.afterUpdateRender = function () {
		if (self.options.afterUpdateRender) self.options.afterUpdateRender.apply(self);	
	}

	self.beforeRenderItems = function () {
		if (self.options.beforeRenderItems) self.options.beforeRenderItems.apply(self);
		if (!Object.keys(self.items).length) return;
		if (self.options.showAllItems) {
			self.matchedIds = Object.keys(self.items);
			self.endIdx = self.matchedIds.length;
			self.startIdx = 1;
		} else {
			self.endIdx = self.startIdx + self.itemPerPage - 1;
			if (self.endIdx > self.matchedIds.length) self.endIdx = self.matchedIds.length;
		}
		if (!self.matchedIds || !self.matchedIds.length) return false;
		self.showingIds = self.matchedIds.slice(self.startIdx - 1, self.endIdx);
		return true;
	}

	self.afterRenderItems = function () {
		if (self.options.afterRenderItems) self.options.afterRenderItems.apply(self);
	}

	self.renderItems = function () {
		// render children item 
		if (self.beforeRenderItems()) {
			var oldIds = [];
			for(var i=0; i<self.shownIds.length; i++) {
				// remove old item on page
				var id = self.shownIds[i];
				if ($.inArray(id, self.showingIds) == -1) {
					// remove html item
					self.getItem(id).$item.remove();
				} else {
					oldIds.push(id);
				}
			}
			// Render for new item
			self.$currentItem = null;
			for(var i=0; i<self.showingIds.length; i++) {
				var id = self.showingIds[i];
				if ($.inArray(id, oldIds) == -1) {
					// render new one
					self.renderItem(id);
				} else {
					var item = self.getItem (id);
					item.updateRender();
					if (self.$currentItem) item.$item.insertAfter(self.$currentItem);
					self.$currentItem = item.$item;
				}
			}
			// update shownIds
			self.shownIds = self.showingIds.slice();
		} else {
			if (self.$wrapper) self.$wrapper.empty();
			self.shownIds = []
		}

		self.afterRenderItems();
	}

	self.renderItem = function (id) {
		var item = self.getItem(id);

		item.render(function($item){
			$item.addClass(self.options.itemClass);
			if (self.$currentItem) $item.insertAfter(self.$currentItem);
			else $item.appendTo(self.$wrapper);
			self.$currentItem = $item;
			return $item;
		});
	}

	self.updateRender = function () {
		self.delayCall('_updateRender', 100);
	}

	self._updateRender = function () {
		var e = $.Event('beforeUpdateRender');
		e.lntype = self.type;
		e.lntarget = self;
		self.$item.trigger(e);

		if (self.options.rerenderWhenUpdate) {
			self.render();
		} else {
			self.beforeUpdateRender();
			self.renderItems();
			self.renderStateFields ();
			self.afterUpdateRender();
		}

		if (self.type === 'LNBase' && self.options.autopage) {
			self.autoPage();
		}

		var e = $.Event('afterUpdateRender');
		e.lntype = self.type;
		e.lntarget = self;
		self.$item.trigger(e);
	}

	self.addItem = function (child, id) {
		if (!id) id = child.data[id];
		child.lnid = id.slugify();
		child.parent = self;
		self.items[child.lnid] = child;
	}

	self.getData = function () {
		return self.data;
	}

	self.setData = function (data) {
		self.data = data;
	}

	self.getField = function (field) {
		if (self[field] !== undefined) return self[field];
		if (self.data[field] !== undefined) return self.data[field];
		// check if path field, eg: stats.HP
		var fields = field.split('.'),
			v = self.data, 
			i=0;
		while(v && i<fields.length) {
			v = v[fields[i++]];
		}
		return v;
	}

	self.getTemplate = function () {
		return self.options.template;
	}

	self.getItem = function (id) {
		return self.items[id.slugify()];
	}

	self.getId = function () {
		return self.lnid;
	}

	self.is = function (check) {
		if (typeof check == 'string') {
			return check === self.type;
		}

		if (check instanceof LNBase) {
			return (check.is(self.type) && check.getId() == self.getId());
		}

		return false;
	}

	// pages
	self.setPage = function (p) {
		if (!p) return;
		self.setQS('page', p);
		self.page = p;
		self.startIdx = (p-1) * self.itemPerPage + 1;
//		if (self.startIdx >= self.matchedIds.length) {
//			// reset to first page
//			self.startIdx = 1;
//			self.page = 1;
//		}
		return self;
	}

  // sticky
  self.sticky = function () {
    if(!isMobile.any ) {
      $(self.options.container).parent().stick_in_parent().trigger('sticky_kit:recalc');
    }
  }

	// auto page
	self.autoPage = function () {
		var	itemWrapper = $(self.options.itemWrapper),
		div = $('<div>', {id:'autopage','page-data': 1, 'style':'clear:both'}),
		reachEnd = 0;

		if ($('#autopage').length) {
			$('#autopage').remove();
			itemWrapper.append(div);
		}
		else {
			itemWrapper.append(div);
		}

		self.startIdx = 1;
		self.endIdx = self.matchedIds.length;

		$(window).on('scroll.page',function () {
			var wheight = $(window).outerHeight(),
			target = $('#autopage').offset().top,
			numpage = parseInt($('#autopage').attr('page-data')),
			start = numpage * self.itemPerPage + 1,
			end = start + self.itemPerPage - 1;

			if (end > self.endIdx ) 
				end = self.endIdx;  

			if( ($(window).scrollTop() + wheight + 400) >= target ) {
				self.showingIds = self.matchedIds.slice(start - 1, end)

				for(var i=0; i<self.showingIds.length; i++) {
					var id = self.showingIds[i];
					self.renderItem(id);
					self.shownIds.push(id);
				}
				
				if (end === self.endIdx) {
					$(window).off('scroll.page')
				} else {
					numpage = numpage + 1;
					$('#autopage').attr('page-data', numpage );
				}

				// for afterAutoPage() callback
				var e = $.Event('afterAutoPage');
				e.lntype = self.type;
				e.lntarget = self;
				self.$item.trigger(e);
			}
		});
	}

	// pages
	self.setLimiter = function (limiter) {
		self.setQS('limiter', limiter);
		self.itemPerPage = parseInt(limiter);
		self.startIdx = (self.page-1) * self.itemPerPage + 1;
		if (self.startIdx >= self.matchedIds.length) {
			// reset to first page
			self.startIdx = 1;
			self.page = 1;
		}
		
		return self;
	}

	// sort
	self.sort = function (field, dir) {
		if (field !== undefined) {
			self.setQS('sort', field);
			self.sortField = field;
		}
		if (dir == 'asc' || dir == 'desc') {
			self.setQS('sortdir', dir);
			self.sortDir = dir;
		}
		var d = self.sortDir == 'desc' ? -1 : 1;
		if (!self.ids.length || (!self.sortField && d==1)) 
			self.ids = Object.keys(self.items);
		
		// do sort
		if (self.sortField) {
			self.ids.sort(function (a, b) {
				var v1 = self.getItem(a).getField(self.sortField),
					v2 = self.getItem(b).getField(self.sortField);
				if (v1 == v2) {
						var t1 = self.getItem(a).getField('id'),
						t2 = self.getItem(b).getField('id');
						return t1 > t2 ? d : -d;
					}
				return v1 > v2 ? d : -d;
			});
		} else if (d == -1) {
			self.ids.reverse();
		}
		return self;
	}

	self.setMatchedItems = function (ids) {
		self.matchedIds = [];
		if (!self.ids.length) self.ids = Object.keys(self.items);
		self.ids.forEach(function(id) {
			if (ids.indexOf(id) != -1) self.matchedIds.push(id);
		})
		return self;
	}

	self.setQS = function (name, value) {
		self.getQS().set(name, value);
	}

	self.getQS = function () {
		if ($.lnqs == undefined) {
			$.lnqs = new LNQueryString();
			$.lnqs.load();
		}
		return $.lnqs;
	}

	self.delayCall = function (func, timeOut) {
		if (self.timeout[func]) clearTimeout(self.timeout[func]);
		if (self[func]) {
			self.timeout[func] = setTimeout(function () {self[func]()}, timeOut);
		}
	}
});
;
var LNFilter = LNBase.extend(function(){
	// private variable
	var self = this,
		$ = jQuery,
		so = setOps;

	self.type = 'LNFilter';

	self.defaultOptions = {
		template: 'filter-list',
		container: '.sidebar',
		itemWrapper: '.filter-list',
		showAllItems: true
	};

	self.lnItems = null;
	self.filterResult = null;

	// store selected value
	self.selectedFilters = {};

	// page
	self.page = 1;

	// Filter base on list data items
	self.addItems = function (lnItems) {
		self.lnItems = lnItems;
	}

	// Filter result
	self.addFilterResult = function (filterResult) {
		self.filterResult = filterResult;
		self.filterResult.setSelectedFilters (self.selectedFilters);
	}


	self.addFilterSingle = function (options) {
		// template
		self.addItem (new LNFilterGroup($.extend(options, {
			template: 'filter.radio',
			type: 'single'
		})), 'filter-' + options.field);
	}

	self.addFilterDropdown = function (options) {
		// template
		self.addItem (new LNFilterGroup($.extend(options, {
			template: 'filter.dropdown',
			type: 'dropdown'
		})), 'filter-' + options.field);
	}

	self.addFilterList = function (options) {
		// template
		self.addItem (new LNFilterGroup($.extend(options, {
			template: 'filter.list',
			type: 'list'
		})), 'filter-' + options.field);
	}


	self.addFilterMultiple = function (options) {
		// template
		self.addItem (new LNFilterGroup($.extend(options, {
			template: 'filter.multiple',
			type: 'multiple'
		})), 'filter-' + options.field);
	}

	self.addFilterValue = function (options) {
		// template
		self.addItem (new LNFilterValue(options), 'filter-' + options.field);
	}

	self.addFilterDate = function (options) {
		// template
		self.addItem (new LNFilterDate(options), 'filter-' + options.field);
	}

	self.addFilterColor = function (options) {
		// template
		self.addItem (new LNFilterGroup($.extend(options, {
			template: 'filter.color',
			type: 'color'
		})), 'filter-' + options.field);
	}

	self.addFilterRange = function (options) {
		// find max value 
		if (!options.max) {
			var field = options.field,
				vals = [];
			for (id in self.lnItems.items) {
				vals.push (self.lnItems.getItem(id).getField(field));
			}
			options.min = Math.min.apply(Math, vals);
			options.max = Math.max.apply(Math, vals);
			// change max min value to float to int.
			if (!!(options.max % 1)) options.max = Math.ceil(options.max);
			if (!!(options.min % 1)) options.min = Math.floor(options.min);
		}

		// template
		self.addItem (new LNFilterRange(options), 'filter-' + options.field);
	}

	// update value for filter type Single & Multiple
	self.updateFilter = function () {
		for (var gid in self.items) {
			var fgroup = self.getItem(gid),
				field = fgroup.options.field,
				type = fgroup.options.type;
			if (fgroup.options.frontend_field)
				frontend_value=fgroup.options.frontend_field;
			else
				frontend_value=fgroup.options.field;
			// show all child
			fgroup.options.showAllItems = true;
			fgroup.options.itemWrapper = '.filter-items';
			// fetch child value from filter data
			if (fgroup.is('LNFilterGroup')) {
				// fetch field values into array
				for (var id in self.lnItems.items) {
					var item = self.lnItems.getItem(id),
						val = item.getField(field);
						frontend_val = item.getField(frontend_value);

					if (val === undefined) continue;
					if ($.isArray(val)) {
						for (var i=0; i<val.length; i++) {
							var v = val[i],
								key = field + '-' + v,
								fitem = fgroup.getItem(key);

							if (!v || v == 'N/A') continue;
							if (!fitem) {
								fitem = new LNFilterItem({
									template: fgroup.getTemplate() + '-item',
									name: field,
									frontend_value: frontend_val[i],
									value: v
								});
								fitem.mids = [];
								fgroup.addItem(fitem, key);
							}
							fitem.mids.push(id);
						}
					} else {
						var v = val,
							key = field + '-' + v,
							fitem = fgroup.getItem(key);
						if (!v || v == 'N/A') continue;	
						if (!fitem) {
							fitem = new LNFilterItem({
								template: fgroup.getTemplate() + '-item',
								name: field,
								value: v
							});
							fitem.mids = [];
							fgroup.addItem(fitem, key);
						}
						fitem.mids.push(id);
					}
				}


				// sort child items
				var keys = Object.keys(fgroup.items);

				// unset this fgroup if less than 1 values
				if (keys.length < 1) {
					delete self.items[gid];
				} else {
					var order = (fgroup.options.order.toUpperCase() == 'DESC') ? -1 : 1;
					keys.sort(function (a, b) {
						return fgroup.getItem(a).options.frontend_value.localeCompare(fgroup.getItem(b).options.frontend_value) * order;
					});
					var _items = {};
					keys.forEach(function (id) {
						_items[id] = fgroup.getItem(id);
					});
					fgroup.items = _items;
				}
			}
		}
		return self;
	}

	self.afterRender = function () {
		// tracking change on filter
		self.$item.on('change', function (e) {
			if (self.options.sticky) {
				self.sticky();
			}
			// update filter selected
			var $value = $(e.target)
				$fgroup = $value.closest('.filter-field'),
				fgroup = self.getItem($fgroup.data('lnid')),
				field = fgroup.options.field,
				type = fgroup.options.type;
			if ($value.is('select')) {
				// find element item
				var val = $value.val(),
					key = field + '-' + val,
					fitem = fgroup.getItem(key);
				// update to selected list
				if (val) {
					self.selectedFilters[field] = fitem;
				} else {
					delete self.selectedFilters[field];
				}
			} else {
				if (fgroup.is('LNFilterValue')) {
					fgroup.value = $value.val();
					if (fgroup.value) {
						self.selectedFilters[field] = fgroup;
					} else {
						delete self.selectedFilters[field];
					}
				}
				if (fgroup.is('LNFilterGroup') && (type == 'multiple' || type == 'color')) {
					var $fitem = $value.closest('.ln-element'),
						fitem = fgroup.getItem($fitem.data('lnid')),
						key = $fitem.data('lnid');//field + '-' + fitem.options.value;
					// toggle
					if ($value.prop('checked')) {
						self.selectedFilters[key] = fitem;
					} else {
						delete self.selectedFilters[key];
					}
				}
				if (fgroup.is('LNFilterGroup') && type == 'single') {
					var $fitem = $value.closest('.ln-element'),
						fitem = fgroup.getItem($fitem.data('lnid'));
					if (fitem) {
						self.selectedFilters[field] = fitem;
					} else {
						delete self.selectedFilters[field];
					}
				}
				if (fgroup.is('LNFilterRange')) {
					fgroup.value = $value.val();
					if (fgroup.value) {
						self.selectedFilters[field] = fgroup;
					} else {
						delete self.selectedFilters[field];
					}
				}				

				if (fgroup.is('LNFilterDate')) {
					fgroup.value = $value.val();
					if (fgroup.value) {
						self.selectedFilters[field] = fgroup;
					} else {
						delete self.selectedFilters[field];
					}
				}
			}

			// using timeout to prevent multiple change in sort time
			clearTimeout(self.filterChangeTimeout);
			self.filterChangeTimeout = setTimeout(function() {self.filterChangeHandle();}, 100);
		});

		self.super.afterRender();

	}

	self.filterChangeHandle = function () {
		// fetch matched item
		var matchedIds = [];
		jQuery('span.color-item-bg').removeClass('color-active');
		if (Object.keys(self.selectedFilters).length) {
			var fields = {},
				ids = [],
				vfields = [];
			for(var prop in self.selectedFilters) {
				var fitem = self.selectedFilters[prop],
					field;

				if (!fitem.is('LNFilterItem')) {
					field = fitem.options.field;
					// process for value & range filter
					// vfields.push(prop);
					var _ids = [];
					for (var id in self.lnItems.items) {
						var item = self.lnItems.getItem(id),
							matched = true;
						if (!fitem.options.match || !fitem.options.match(item, field, fitem.value))
							matched = false;
						if (matched) _ids.push(id);
					}
					fields[field] = _ids;
				} else {
					field = fitem.parent.options.field;
					if (fitem.options.template === 'filter.color-item')
						fitem.$item.find('.color-item-bg').addClass('color-active');
					fields[field] = fields[field] ? setOps.union(fields[field], fitem.mids) : fitem.mids;
				}
				ids = setOps.union(ids, fields[field]);
			}

			var t = 0;
			// find matched items
			for(var field in fields) {
				matchedIds = t ? setOps.intersection(matchedIds, fields[field]) : fields[field];
				t++;
			}

			// update counter for this field
			for (var gid in self.items) {
				var fgroup = self.getItem(gid),
					field = fgroup.options.field;
					fmatchedIds = [];

				// reset before count
				for (var fid in fgroup.items) {
					fgroup.getItem(fid).mcount = 0;
				}

				if (field in fields) {
					if (Object.keys(fields).length == 1) {
						// use global count
						// update counter for each item
						for (var fid in fgroup.items) {
							fgroup.getItem(fid).mcount = fgroup.getItem(fid).mids.length;
						}
					} else {
						// find matched items except this condition
						var t = 0
						for(var f in fields) {
							if (f == field) continue;
							fmatchedIds = t ? setOps.intersection(fmatchedIds, fields[f]):fields[f];
							t++
						}
					}
				} else {
					fmatchedIds = matchedIds;
				}

				if (fmatchedIds.length) {
					// reset before count
					fmatchedIds.forEach(function(id) {	
						var item = self.lnItems.getItem(id),
							val = item.getField(field);

						if ($.isArray(val)) {							
							val.forEach(function(v){
								var fitem = fgroup.getItem(field + '-' + v);
								if (fitem) fitem.mcount += 1;
							});
						} else {
							var v = val,
								key = field + '-' + v,
								fitem = fgroup.getItem(key);
							if (fitem) fitem.mcount += 1;
						}
					})
				}
			}
		} else {
			matchedIds = Object.keys(self.lnItems.items);
			// reset counter
			for (gid in self.items) {
				var fgroup = self.getItem(gid);
				for (var fid in fgroup.items) {
					fgroup.getItem(fid).mcount = fgroup.getItem(fid).mids.length;
				}
			}
		}
		self.lnItems.setMatchedItems(matchedIds);
		
		
		self.lnItems.setPage(self.page);
		self.page = 1;
		self.lnItems.updateRender();

		self.updateRender();
		self.updateFilterResult();
	}

	self.updateFilterResult = function () {
		if (self.filterResult) {
			self.filterResult.updateRender();
		}
	}

	self.load = function () {
		var qs = self.getQS().qs,
			filtered = false;
		for (var gid in self.items) {
			var fgroup = self.getItem(gid),
				field = fgroup.options.title.slugify();
			if (qs[field]) {
				fgroup.setValue(qs[field]);
				filtered = true;
			}
		}

		return self;
	}
});;
var LNFilterGroup = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterGroup';

	self.options = null;
	self.defaultOptions = {
		class: 'filter-field',
		order: 'ASC'
	};
	
	self.afterRender = function () {
		// easy way to change the category tree name.
		// only use field name that we can detect it's category tree.
		if (self.options.field == 'attr.cat.value' || self.options.field == 'attr.category.value') {
			switch (self.options.type) {
				case 'multiple': 
					self.$item.find('input').each(function() {
						$lv = (jQuery(this).next().text().match(/ » /g) || []).length+1;
						jQuery(this).parents('li').addClass('lv-'+$lv);
						jQuery(this).next().text(jQuery(this).next().text().replace(/^(.*)?» /, ''));
					});
					break;
				case 'single':
					self.$item.find('input').each(function() {
						$lv = (jQuery(this).next().text().match(/ » /g) || []).length+1;
						jQuery(this).parents('li').addClass('lv-'+$lv);
						jQuery(this).next().text(jQuery(this).next().text().replace(/^(.*)?» /, ''));
					});
					break;

				case 'dropdown':
					self.$item.find('option').each(function() {
						$lv = (jQuery(this).text().match(/ » /g) || []).length+1;
						$space = '';
						for (i=1;i<$lv;i++) {
							$space += String.fromCharCode(160)+''+String.fromCharCode(160)+''+String.fromCharCode(160);
						}
						jQuery(this).text($space+''+jQuery(this).text().replace(/^(.*)?» /, ''));
					});
					break;
				case 'color':
				case 'list':
					break;
			}
		}
		if (self.options.type == 'color') {
			self.$item.find('span.color-item-bg').each(function() {
				if ((JAnameColor[jQuery(this).data('bgcolor').toLowerCase().trim()]) != undefined) {
					jQuery(this).css('background-color', JAnameColor[jQuery(this).data('bgcolor').toLowerCase().trim()]);
				} else {
					jQuery(this).css('background-color', (jQuery(this).data('bgcolor').toLowerCase().trim().indexOf('#') === -1 ? '#' : '')+jQuery(this).data('bgcolor').toLowerCase().trim());
				}
			});
		}
	}

	self.reset = function () {
		/*
		switch (self.options.type) {
			case 'multiple': 
				self.$item.find('input').prop('checked', false).trigger('change');
				break;
			case 'single':
				self.$item.find('input').first().prop('checked', true).trigger('change');
				break;

			case 'dropdown':
				self.$item.find('select').val('').trigger('change');
				break;

			case 'list':
				break;
		}*/
		self.setValue('');
	}

	self.setValue = function (value) {
		switch (self.options.type) {
			case 'color':
			case 'multiple': 
				var vals = value.split(',');
				self.$item.find('input').prop('checked', false).filter(function(){
					return vals.indexOf (this.value) != -1
				}).prop('checked', true).trigger('change');
				break;
			case 'single':
				self.$item.find('input[value="' + value + '"]').prop('checked', true).trigger('change');
				break;

			case 'dropdown':
				self.$item.find('select').val(value).trigger('change');
				break;

			case 'list':
				break;
		}		
	}
});
var LNFilterItem = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterItem';

	self.options = null;
	self.defaultOptions = {
		class: 'filter-item'
	};


	self.updateRender = function () {
		// update textnode to replce counter
		self.$item.find(":not(iframe)").addBack().contents().filter(function(){
			return this.nodeType == 3;
		}).each(function(){
			var $this = $(this);
			$this.replaceWith($this.text().replace(/\(.*\)$/, '(' + self.mcount + ')'));
		});

		// add empty class into empty group
		if (!self.mcount) self.$item.addClass('empty');
		else self.$item.removeClass('empty');

		// call super
		self.super.updateRender();
	}

	self.reset = function () {
		if (self.parent.options.type == 'multiple' || self.parent.options.type == 'color'){
			self.$item.find('input').prop('checked', false).trigger('change');
		} else {
			self.parent.reset();
		}
	}
});

var LNFilterColor = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterColor';

	self.options = null;
	self.defaultOptions = {
		template: 'filter.color',
		class: 'filter-input filter-field filter-item',
		match: function (item, field, val) {
			return null;
		}
	};

	self.afterRender = function () {
		
	}

	self.reset = function () {
		
	}

	self.setValue = function (value) {
		
	}
});

var LNFilterDate = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterDate';

	self.options = null;

	self.defaultOptions = {
		template: 'filter.date',
		class: 'filter-input filter-field',
		match: function (item, field, val) {
			// prevent error when the item do not had value on this field.
			if (item.getField(field) == undefined == true || item.getField(field) == false || item.getField(field) == undefined || item.getField(field) == 'undefined') return null; 

			var check = false,
			from = self.$item.find('.jafrom').val(),
			to = self.$item.find('.jato').val();
			from = new Date(from).getTime() / 1000;
			to = new Date(to).getTime() / 1000;
			for (i=0;i<item.getField(field).length;i++) {
				itemval = item.getField(field)[i];
				if (itemval >= from && itemval <= to) {
					check = true;
				}
			}
			return check;
		}
	};
	
	self.afterRender = function () {
		self.$item.find('input').each(function(i, e){
			$(this).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yy-mm-dd",
				onSelect : function(s, o){
					var from = self.$item.find('.jafrom').val(),
					to = self.$item.find('.jato').val();
					if (from !== '' && to !== '') {
						$(this).trigger('change');
					} 
				}
			});
		});
	}

	self.reset = function () {
		self.$item.find('input').each(function(i, e){
			$(e).val('').trigger('change');
		});
	}	

	self.setValue = function (value) {
		vals = value.split(',');
		self.$item.find('input').each(function(i, e){
			$(e).val(vals[i]);
			$(e).trigger('change');
		});

	}
});


var LNFilterRange = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterRange';

	self.options = null;
	self.defaultOptions = {
		template: 'filter.range',
		class: 'filter-range filter-field',
		min: 0,
		max: 0,
		match: function (item, field, val) {
			var itemVal = parseFloat( item.getField(field) );
			return (itemVal >= val[0] && (val[1] == self.options.max || itemVal <= val[1]));
		}
	};

	self.afterRender = function () {
		self.$slider = self.$item.find('.range-item');
		if (self.$slider.length) {
			self.$slider.slider({
				range: true,
				min: self.options.min,
				max: self.options.max,
				isRTL: true, // RTL
				values: [ self.options.min, self.options.max ],
				slide: function( event, ui ) {
					self.$item.find('.range-value0').html(ui.values[0]);
					self.$item.find('.range-value1').html(ui.values[1]);
				},
				change: function( event, ui ) {
					clearTimeout(self.changeTimeout);
					self.changeTimeout = setTimeout(function(){self.triggerChange();}, 100);
					self.$item.find('.range-value0').html(ui.values[0]);
					self.$item.find('.range-value1').html(ui.values[1]);
				}
		    });
		    // first value
			self.$item.find('.range-value0').html(self.options.min);
			self.$item.find('.range-value1').html(self.options.max);
		}
	}

	self.reset = function () {
		self.$slider.slider( "values", [self.options.min, self.options.max]);
	}

	self.setValue = function (value) {
		self.$slider.slider( "values", value.split(','));
	}

	self.triggerChange = function () {
		var values = self.$slider.slider("values");
		self.$slider.val((values[0] == self.options.min && values[1] == self.options.max) ? null : values);
		var e = $.Event( "change", { target: self.$slider[0] } );
		self.$slider.trigger('change', e);
	}

	self.toString = function () {
		if (self.options.toString) self.options.toString.apply(self);
		return self.$slider.slider("values").join (' - ');
	}	
});
var LNFilterValue = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNFilterValue';

	self.options = null;
 
	self.defaultOptions = {
		template: 'filter.value',
		class: 'filter-input filter-field',
		match: function (item, field, val) {
			if (item.getField(field) == undefined || item.getField(field) == 'undefined') return null; // prevent error when the item do not had value on this field.
			val = val.replace(/([^a-zA-Z0-9])/g, '\\$1'); // addslashes to all special characters.
			return item.getField(field).match(new RegExp(val, 'i'));
		}
	};

	self.reset = function () {
		self.$item.find('input').val('').trigger('change');
	}	

	self.setValue = function (value) {
		self.$item.find('input').val(value).trigger('change');
	}	
});
var LNItem = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNItem';

	self.options = null;
	self.defaultOptions = {
		class: 'ln-item'
	};
});
var LNQueryString = Class.extend(function(){
	// private variable
	var self = this,
		$ = jQuery;
	self.qs = {};

	self.defaultValues = {};

	self.load = function () {
		// handle first visit, reselect
		var hash = location.hash;
		var qs = {};
		location.hash.substr(1).split('&').forEach(function(nvp){
			var nv = nvp.split('=');
			if (nv.length == 2) qs[nv[0]] = decodeURIComponent(nv[1]);
		});
		self.qs = qs;

		// register event to update update url hash
		$(document).on('afterRender', function (e) {
			if (e.lntype === 'LNToolbar' || e.lntype === 'LNSelected') {
        var hash = [], page = 0;
        for (var name in self.qs) {
          if (name == 'page') {
            page = self.qs[name];
            continue;
          }
          if (self.defaultValues[name] == self.qs[name]) continue;
          hash.push(name.slugify() + '=' + encodeURIComponent(self.qs[name]));
        }
        var curHash = hash.slice(0);
        if (page) curHash.push('page=' + page);
				
				/* global filter_url */
        if ($('#jamegafilter-search-btn').length > 0) {
          var surl = filter_url + '#' + curHash.join('&'); 
          $('#jamegafilter-search-btn').attr('href', surl );
        } else {
  				location.hash = curHash.join('&');
  				// update href for page links
  				var baseLink = '#' + (hash.length ? hash.join('&') + '&' : '');
  				$('a.page').each(function(){
  					var $a = $(this),
  						page = $a.data('value');
  					$a.attr('href', baseLink + 'page=' + page);
  				})
        }					
			}
		})
	
	}

	self.set = function (name, value) {
		if (value) {
			self.qs[name] = value;
		} else {
			delete self.qs[name];
		}
	}

	self.setDefault = function (defaultValues) {
		self.defaultValues = defaultValues;
	}
});
var LNSelected = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNSelected';

	self.options = null;
	self.defaultOptions = {
		template: 'filter-selected',
		class: 'selected-filters'
	};

	self.selectedFilters = null;
	self.values = {};

	self.filterFields = [];

	self.setSelectedFilters = function (selectedFilters) {
		self.selectedFilters = selectedFilters;
	}

	self.setFilter = function (lnFilter) {
		self.lnFilter = lnFilter;
		self.filterFields = [];
		for (var gid in lnFilter.items) {
			var fgroup = lnFilter.getItem(gid),
				field = fgroup.options.title;
			self.filterFields.push(field);
		}
	}

	self.updateRender = function () {
		self.render();
	}

	self.beforeRender = function () {
		// parse selected filters to display
		self.values = {};
		for(var prop in self.selectedFilters) {
			var fitem = self.selectedFilters[prop], field, sval, rval;
			if (fitem.is('LNFilterItem')) {
				field = fitem.parent.options.title;
				rval = fitem.options.value;
				sval = fitem.options.frontend_value;
				if (fitem.options.name == 'attr.cat.value' || fitem.options.name == 'attr.category.value')
					sval = sval.replace(/^(.*)?\&raquo\; /, '').replace(/^(.*)?» /, '');

				if (fitem.options.template === 'filter.color-item') {
					if (JAnameColor[sval.toLowerCase().trim()] != undefined) {
						sval = '<i class="fa fa-circle-o" style="color:'+JAnameColor[sval.toLowerCase().trim()]+';" aria-hidden="true"></i>';
					} else {
						sval = '<i class="fa fa-circle-o" style="color:'+(sval.toLowerCase().trim().indexOf('#') === -1 ? '#' : '')+sval.toLowerCase().trim()+';" aria-hidden="true"></i>';
					}
				}

			} else if (fitem.is('LNFilterRange')) {
				field = fitem.options.title;
				sval = fitem.toString();
				rval = fitem.value;
			} else if (fitem.is('LNFilterDate')) {
				field = fitem.options.title;
				var from = fitem.$item.find('.jafrom').val(),
				to = fitem.$item.find('.jato').val();
				sval = from+' '+Joomla.JText._('COM_JAMEGAFILTER_TO')+' '+ to;
				rval = from+','+to;
			} else {
				field = fitem.options.title;
				rval = fitem.value;
				sval = rval;
			}
			if (!self.values[field]) self.values[field] = [];
			self.values[field].push({prop: prop, value: sval, raw_value: rval});
		}

		// update url hash
		if (self.registeredClearFilterEvent) {
			var hash = [];
			self.filterFields.forEach(function(field) {
				var val = null;
				if (self.values[field]) {
					var vals = [];
					self.values[field].forEach(function(val){
						vals.push(val.raw_value);
					});
					val = vals.join(',');
				}
				self.setQS(field.slugify(), val);
			})
		}

		//self.super.beforeRender();
	}

	self.afterRender = function () {
		if (Object.keys(self.values).length) 
			self.$item.parent().removeClass ('empty');
		else 
			self.$item.parent().addClass ('empty');
		self.super.afterRender();
		self.registerClearFilter();
	}

	self.registerClearFilter = function () {
		if (self.registeredClearFilterEvent) return;
		self.registeredClearFilterEvent = true;
		// listening click on filter-clear
		$(self.options.container).on('click', function (e) {
			var $item = $(e.target);
			if ($item.is ('.clear-filter')) {
				var lnprop = $item.data('lnprop'),
					item = self.selectedFilters[lnprop];
				item.reset();
			}
			if ($item.is ('.clear-all-filter')) {
				self.resetAll();
			}
		})
	}

	self.resetAll = function () {
		for(var prop in self.selectedFilters) {
			var fitem = self.selectedFilters[prop];
			fitem.reset();
		}
	}

}) ;
var LNToolbar = LNBase.extend(function() {
	// private variable
	var self = this,
		$ = jQuery;

	self.type = 'LNToolbar';

	self.options = null;
	self.defaultOptions = {
		template: 'product-toolbar',
		class: 'products-toolbar',
		rerenderWhenUpdate: true
	};

	self.lnItems = null;

	self.startIdx = 0;
	self.endIdx = 0;
	self.totalItems = 0;
	self.page = 0;
	self.itemPerPage = 0;
	self.nextPage = null;
	self.sortDir = '';

	self.constructor = function (options) {
		self.super.constructor(options);
		// get from config
		self.sortByOptions = self.options.config.sortByOptions;
		self.productsPerPageAllowed = self.options.config.productsPerPageAllowed;
		self.itemPerPage = self.productsPerPageAllowed[0];
		self.autopage = self.options.config.autopage;
	}

	self.beforeRender = function () {
		self.startIdx = self.lnItems.matchedIds.length ? self.lnItems.startIdx:0;
		self.endIdx = self.lnItems.endIdx;
		self.totalItems = self.lnItems.matchedIds.length;
		self.curPage = parseInt(self.lnItems.page);
		self.itemPerPage = self.lnItems.itemPerPage;
		// calculate pages, prevPage, nextPage
		var pages = Math.ceil(self.totalItems / self.itemPerPage);
		self.startPage = 1;
		self.endPage = pages;
		self.prevPage = self.curPage > 1 ? self.curPage - 1 : 1;
		self.nextPage = self.curPage < pages ? self.curPage + 1 : pages;

		self.pages = null;
		if (pages > 1) {
			self.pages = [];
			var start, end;
			start = self.curPage - 2;
			if (start < 1) start = 1;
			end = 5 + start;
			if (end > pages) {
				end = pages;
				start = end - 5;
				if (start < 1) start = 1;
			}

			for (var i=start; i<=end; i++) self.pages.push(i);
		}
		self.sortField = self.lnItems.sortField;
		self.sortDir = self.lnItems.sortDir == 'desc' ? 'asc' : 'desc';
	}

	// Filter base on list data items
	self.addItems = function (lnItems) {
		self.lnItems = lnItems;
	}

	self.setPage = function (p) {
		self.page = p;
	}

	self.afterRender = function () {
		if (!self.actionHandled) {
			self.actionHandled = true;
			self.$item.parent().on('click', function (e) {
				var $btn = $(e.target).closest('[data-action]'),
					action = 'do' + $btn.data('action');
				if (!self[action]) return false;
				var value = $btn.data('value');
				self[action](value);
				return false;
			}).on('change', function (e) {
				var $elem = $(e.target);
				if ($elem.prop('tagName') == 'SELECT') {
					var $btn = $elem.find(":selected"),
						action = 'do' + $btn.data('action');
					if (!self[action]) return false;
					var value = $btn.data('value');
					self[action](value);
					return false;
				}
			});
		}
	}

	self.dopage = function (value) {
		if (value != self.lnItems.page){
			self.lnItems.setPage(value).updateRender();
		}
	}

	self.dosort = function (value) {
		if (value != self.lnItems.sortField) {
			self.lnItems.sort(value).setMatchedItems(self.lnItems.matchedIds).updateRender();
		}
	}

	self.dosortdir = function (value) {
		sorDir = self.lnItems.sortDir == 'desc' ? 'asc' : 'desc';
		self.lnItems.sort(undefined, sorDir).setMatchedItems(self.lnItems.matchedIds).updateRender();
	}

	self.dolimiter = function (value) {
		if (value != self.lnItems.itemPerPage) {
			self.lnItems.setLimiter(value).updateRender();
		}
	}

	self.load = function () {
		var qs = self.getQS();
		// set default toolbar value
		qs.setDefault({
			page: 1,
			limiter: self.productsPerPageAllowed[0],
			sort: self.sortByOptions[0].field,
			sortdir: 'asc'
		})
		for (var name in qs.qs) {
			var value = qs.qs[name],
				action = 'do' + name;
			if (self[action]) {
				self[action](value);
			}
		}
	}

});
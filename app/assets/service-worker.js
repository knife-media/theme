try{self["workbox:core:6.5.2"]&&_()}catch(e){}const e=(e,...t)=>{let s=e;return t.length>0&&(s+=` :: ${JSON.stringify(t)}`),s};class t extends Error{constructor(t,s){super(e(t,s)),this.name=t,this.details=s}}try{self["workbox:routing:6.5.2"]&&_()}catch(e){}const s=e=>e&&"object"==typeof e?e:{handle:e};class n{constructor(e,t,n="GET"){this.handler=s(t),this.match=e,this.method=n}setCatchHandler(e){this.catchHandler=s(e)}}class i extends n{constructor(e,t,s){super((({url:t})=>{const s=e.exec(t.href);if(s&&(t.origin===location.origin||0===s.index))return s.slice(1)}),t,s)}}class r{constructor(){this.t=new Map,this.i=new Map}get routes(){return this.t}addFetchListener(){self.addEventListener("fetch",(e=>{const{request:t}=e,s=this.handleRequest({request:t,event:e});s&&e.respondWith(s)}))}addCacheListener(){self.addEventListener("message",(e=>{if(e.data&&"CACHE_URLS"===e.data.type){const{payload:t}=e.data,s=Promise.all(t.urlsToCache.map((t=>{"string"==typeof t&&(t=[t]);const s=new Request(...t);return this.handleRequest({request:s,event:e})})));e.waitUntil(s),e.ports&&e.ports[0]&&s.then((()=>e.ports[0].postMessage(!0)))}}))}handleRequest({request:e,event:t}){const s=new URL(e.url,location.href);if(!s.protocol.startsWith("http"))return;const n=s.origin===location.origin,{params:i,route:r}=this.findMatchingRoute({event:t,request:e,sameOrigin:n,url:s});let o=r&&r.handler;const a=e.method;if(!o&&this.i.has(a)&&(o=this.i.get(a)),!o)return;let c;try{c=o.handle({url:s,request:e,event:t,params:i})}catch(e){c=Promise.reject(e)}const f=r&&r.catchHandler;return c instanceof Promise&&(this.o||f)&&(c=c.catch((async n=>{if(f)try{return await f.handle({url:s,request:e,event:t,params:i})}catch(e){e instanceof Error&&(n=e)}if(this.o)return this.o.handle({url:s,request:e,event:t});throw n}))),c}findMatchingRoute({url:e,sameOrigin:t,request:s,event:n}){const i=this.t.get(s.method)||[];for(const r of i){let i;const o=r.match({url:e,sameOrigin:t,request:s,event:n});if(o)return i=o,(Array.isArray(i)&&0===i.length||o.constructor===Object&&0===Object.keys(o).length||"boolean"==typeof o)&&(i=void 0),{route:r,params:i}}return{}}setDefaultHandler(e,t="GET"){this.i.set(t,s(e))}setCatchHandler(e){this.o=s(e)}registerRoute(e){this.t.has(e.method)||this.t.set(e.method,[]),this.t.get(e.method).push(e)}unregisterRoute(e){if(!this.t.has(e.method))throw new t("unregister-route-but-not-found-with-method",{method:e.method});const s=this.t.get(e.method).indexOf(e);if(!(s>-1))throw new t("unregister-route-route-not-registered");this.t.get(e.method).splice(s,1)}}let o;const a={googleAnalytics:"googleAnalytics",precache:"precache-v2",prefix:"workbox",runtime:"runtime",suffix:"undefined"!=typeof registration?registration.scope:""},c=e=>[a.prefix,e,a.suffix].filter((e=>e&&e.length>0)).join("-"),f=e=>e||c(a.precache),h=e=>e||c(a.runtime);function l(e,t){const s=t();return e.waitUntil(s),s}try{self["workbox:precaching:6.5.2"]&&_()}catch(e){}function u(e){if(!e)throw new t("add-to-cache-list-unexpected-type",{entry:e});if("string"==typeof e){const t=new URL(e,location.href);return{cacheKey:t.href,url:t.href}}const{revision:s,url:n}=e;if(!n)throw new t("add-to-cache-list-unexpected-type",{entry:e});if(!s){const e=new URL(n,location.href);return{cacheKey:e.href,url:e.href}}const i=new URL(n,location.href),r=new URL(n,location.href);return i.searchParams.set("__WB_REVISION__",s),{cacheKey:i.href,url:r.href}}class d{constructor(){this.updatedURLs=[],this.notUpdatedURLs=[],this.handlerWillStart=async({request:e,state:t})=>{t&&(t.originalRequest=e)},this.cachedResponseWillBeUsed=async({event:e,state:t,cachedResponse:s})=>{if("install"===e.type&&t&&t.originalRequest&&t.originalRequest instanceof Request){const e=t.originalRequest.url;s?this.notUpdatedURLs.push(e):this.updatedURLs.push(e)}return s}}}class w{constructor({precacheController:e}){this.cacheKeyWillBeUsed=async({request:e,params:t})=>{const s=(null==t?void 0:t.cacheKey)||this.h.getCacheKeyForURL(e.url);return s?new Request(s,{headers:e.headers}):e},this.h=e}}let p;async function m(e,s){let n=null;if(e.url){n=new URL(e.url).origin}if(n!==self.location.origin)throw new t("cross-origin-copy-response",{origin:n});const i=e.clone(),r={headers:new Headers(i.headers),status:i.status,statusText:i.statusText},o=s?s(r):r,a=function(){if(void 0===p){const e=new Response("");if("body"in e)try{new Response(e.body),p=!0}catch(e){p=!1}p=!1}return p}()?i.body:await i.blob();return new Response(a,o)}function b(e,t){const s=new URL(e);for(const e of t)s.searchParams.delete(e);return s.href}class g{constructor(){this.promise=new Promise(((e,t)=>{this.resolve=e,this.reject=t}))}}const y=new Set;try{self["workbox:strategies:6.5.2"]&&_()}catch(e){}function v(e){return"string"==typeof e?new Request(e):e}class k{constructor(e,t){this.l={},Object.assign(this,t),this.event=t.event,this.u=e,this.p=new g,this.m=[],this.g=[...e.plugins],this.v=new Map;for(const e of this.g)this.v.set(e,{});this.event.waitUntil(this.p.promise)}async fetch(e){const{event:s}=this;let n=v(e);if("navigate"===n.mode&&s instanceof FetchEvent&&s.preloadResponse){const e=await s.preloadResponse;if(e)return e}const i=this.hasCallback("fetchDidFail")?n.clone():null;try{for(const e of this.iterateCallbacks("requestWillFetch"))n=await e({request:n.clone(),event:s})}catch(e){if(e instanceof Error)throw new t("plugin-error-request-will-fetch",{thrownErrorMessage:e.message})}const r=n.clone();try{let e;e=await fetch(n,"navigate"===n.mode?void 0:this.u.fetchOptions);for(const t of this.iterateCallbacks("fetchDidSucceed"))e=await t({event:s,request:r,response:e});return e}catch(e){throw i&&await this.runCallbacks("fetchDidFail",{error:e,event:s,originalRequest:i.clone(),request:r.clone()}),e}}async fetchAndCachePut(e){const t=await this.fetch(e),s=t.clone();return this.waitUntil(this.cachePut(e,s)),t}async cacheMatch(e){const t=v(e);let s;const{cacheName:n,matchOptions:i}=this.u,r=await this.getCacheKey(t,"read"),o=Object.assign(Object.assign({},i),{cacheName:n});s=await caches.match(r,o);for(const e of this.iterateCallbacks("cachedResponseWillBeUsed"))s=await e({cacheName:n,matchOptions:i,cachedResponse:s,request:r,event:this.event})||void 0;return s}async cachePut(e,s){const n=v(e);var i;await(i=0,new Promise((e=>setTimeout(e,i))));const r=await this.getCacheKey(n,"write");if(!s)throw new t("cache-put-with-no-response",{url:(o=r.url,new URL(String(o),location.href).href.replace(new RegExp(`^${location.origin}`),""))});var o;const a=await this.k(s);if(!a)return!1;const{cacheName:c,matchOptions:f}=this.u,h=await self.caches.open(c),l=this.hasCallback("cacheDidUpdate"),u=l?await async function(e,t,s,n){const i=b(t.url,s);if(t.url===i)return e.match(t,n);const r=Object.assign(Object.assign({},n),{ignoreSearch:!0}),o=await e.keys(t,r);for(const t of o)if(i===b(t.url,s))return e.match(t,n)}(h,r.clone(),["__WB_REVISION__"],f):null;try{await h.put(r,l?a.clone():a)}catch(e){if(e instanceof Error)throw"QuotaExceededError"===e.name&&await async function(){for(const e of y)await e()}(),e}for(const e of this.iterateCallbacks("cacheDidUpdate"))await e({cacheName:c,oldResponse:u,newResponse:a.clone(),request:r,event:this.event});return!0}async getCacheKey(e,t){const s=`${e.url} | ${t}`;if(!this.l[s]){let n=e;for(const e of this.iterateCallbacks("cacheKeyWillBeUsed"))n=v(await e({mode:t,request:n,event:this.event,params:this.params}));this.l[s]=n}return this.l[s]}hasCallback(e){for(const t of this.u.plugins)if(e in t)return!0;return!1}async runCallbacks(e,t){for(const s of this.iterateCallbacks(e))await s(t)}*iterateCallbacks(e){for(const t of this.u.plugins)if("function"==typeof t[e]){const s=this.v.get(t),n=n=>{const i=Object.assign(Object.assign({},n),{state:s});return t[e](i)};yield n}}waitUntil(e){return this.m.push(e),e}async doneWaiting(){let e;for(;e=this.m.shift();)await e}destroy(){this.p.resolve(null)}async k(e){let t=e,s=!1;for(const e of this.iterateCallbacks("cacheWillUpdate"))if(t=await e({request:this.request,response:t,event:this.event})||void 0,s=!0,!t)break;return s||t&&200!==t.status&&(t=void 0),t}}class R extends class{constructor(e={}){this.cacheName=h(e.cacheName),this.plugins=e.plugins||[],this.fetchOptions=e.fetchOptions,this.matchOptions=e.matchOptions}handle(e){const[t]=this.handleAll(e);return t}handleAll(e){e instanceof FetchEvent&&(e={event:e,request:e.request});const t=e.event,s="string"==typeof e.request?new Request(e.request):e.request,n="params"in e?e.params:void 0,i=new k(this,{event:t,request:s,params:n}),r=this.R(i,s,t);return[r,this.q(r,i,s,t)]}async R(e,s,n){let i;await e.runCallbacks("handlerWillStart",{event:n,request:s});try{if(i=await this.U(s,e),!i||"error"===i.type)throw new t("no-response",{url:s.url})}catch(t){if(t instanceof Error)for(const r of e.iterateCallbacks("handlerDidError"))if(i=await r({error:t,event:n,request:s}),i)break;if(!i)throw t}for(const t of e.iterateCallbacks("handlerWillRespond"))i=await t({event:n,request:s,response:i});return i}async q(e,t,s,n){let i,r;try{i=await e}catch(r){}try{await t.runCallbacks("handlerDidRespond",{event:n,request:s,response:i}),await t.doneWaiting()}catch(e){e instanceof Error&&(r=e)}if(await t.runCallbacks("handlerDidComplete",{event:n,request:s,response:i,error:r}),t.destroy(),r)throw r}}{constructor(e={}){e.cacheName=f(e.cacheName),super(e),this.L=!1!==e.fallbackToNetwork,this.plugins.push(R.copyRedirectedCacheableResponsesPlugin)}async U(e,t){const s=await t.cacheMatch(e);return s||(t.event&&"install"===t.event.type?await this._(e,t):await this.C(e,t))}async C(e,s){let n;const i=s.params||{};if(!this.L)throw new t("missing-precache-entry",{cacheName:this.cacheName,url:e.url});{const t=i.integrity,r=e.integrity,o=!r||r===t;n=await s.fetch(new Request(e,{integrity:r||t})),t&&o&&(this.O(),await s.cachePut(e,n.clone()))}return n}async _(e,s){this.O();const n=await s.fetch(e);if(!await s.cachePut(e,n.clone()))throw new t("bad-precaching-response",{url:e.url,status:n.status});return n}O(){let e=null,t=0;for(const[s,n]of this.plugins.entries())n!==R.copyRedirectedCacheableResponsesPlugin&&(n===R.defaultPrecacheCacheabilityPlugin&&(e=s),n.cacheWillUpdate&&t++);0===t?this.plugins.push(R.defaultPrecacheCacheabilityPlugin):t>1&&null!==e&&this.plugins.splice(e,1)}}R.defaultPrecacheCacheabilityPlugin={cacheWillUpdate:async({response:e})=>!e||e.status>=400?null:e},R.copyRedirectedCacheableResponsesPlugin={cacheWillUpdate:async({response:e})=>e.redirected?await m(e):e};class q{constructor({cacheName:e,plugins:t=[],fallbackToNetwork:s=!0}={}){this.N=new Map,this.j=new Map,this.K=new Map,this.u=new R({cacheName:f(e),plugins:[...t,new w({precacheController:this})],fallbackToNetwork:s}),this.install=this.install.bind(this),this.activate=this.activate.bind(this)}get strategy(){return this.u}precache(e){this.addToCacheList(e),this.S||(self.addEventListener("install",this.install),self.addEventListener("activate",this.activate),this.S=!0)}addToCacheList(e){const s=[];for(const n of e){"string"==typeof n?s.push(n):n&&void 0===n.revision&&s.push(n.url);const{cacheKey:e,url:i}=u(n),r="string"!=typeof n&&n.revision?"reload":"default";if(this.N.has(i)&&this.N.get(i)!==e)throw new t("add-to-cache-list-conflicting-entries",{firstEntry:this.N.get(i),secondEntry:e});if("string"!=typeof n&&n.integrity){if(this.K.has(e)&&this.K.get(e)!==n.integrity)throw new t("add-to-cache-list-conflicting-integrities",{url:i});this.K.set(e,n.integrity)}if(this.N.set(i,e),this.j.set(i,r),s.length>0){const e=`Workbox is precaching URLs without revision info: ${s.join(", ")}\nThis is generally NOT safe. Learn more at https://bit.ly/wb-precache`;console.warn(e)}}}install(e){return l(e,(async()=>{const t=new d;this.strategy.plugins.push(t);for(const[t,s]of this.N){const n=this.K.get(s),i=this.j.get(t),r=new Request(t,{integrity:n,cache:i,credentials:"same-origin"});await Promise.all(this.strategy.handleAll({params:{cacheKey:s},request:r,event:e}))}const{updatedURLs:s,notUpdatedURLs:n}=t;return{updatedURLs:s,notUpdatedURLs:n}}))}activate(e){return l(e,(async()=>{const e=await self.caches.open(this.strategy.cacheName),t=await e.keys(),s=new Set(this.N.values()),n=[];for(const i of t)s.has(i.url)||(await e.delete(i),n.push(i.url));return{deletedURLs:n}}))}getURLsToCacheKeys(){return this.N}getCachedURLs(){return[...this.N.keys()]}getCacheKeyForURL(e){const t=new URL(e,location.href);return this.N.get(t.href)}getIntegrityForCacheKey(e){return this.K.get(e)}async matchPrecache(e){const t=e instanceof Request?e.url:e,s=this.getCacheKeyForURL(t);if(s){return(await self.caches.open(this.strategy.cacheName)).match(s)}}createHandlerBoundToURL(e){const s=this.getCacheKeyForURL(e);if(!s)throw new t("non-precached-url",{url:e});return t=>(t.request=new Request(e),t.params=Object.assign({cacheKey:s},t.params),this.strategy.handle(t))}}let U;const L=()=>(U||(U=new q),U);class C extends n{constructor(e,t){super((({request:s})=>{const n=e.getURLsToCacheKeys();for(const i of function*(e,{ignoreURLParametersMatching:t=[/^utm_/,/^fbclid$/],directoryIndex:s="index.html",cleanURLs:n=!0,urlManipulation:i}={}){const r=new URL(e,location.href);r.hash="",yield r.href;const o=function(e,t=[]){for(const s of[...e.searchParams.keys()])t.some((e=>e.test(s)))&&e.searchParams.delete(s);return e}(r,t);if(yield o.href,s&&o.pathname.endsWith("/")){const e=new URL(o.href);e.pathname+=s,yield e.href}if(n){const e=new URL(o.href);e.pathname+=".html",yield e.href}if(i){const e=i({url:r});for(const t of e)yield t.href}}(s.url,t)){const t=n.get(i);if(t){return{cacheKey:t,integrity:e.getIntegrityForCacheKey(t)}}}}),e.strategy)}}function E(e){const s=L();!function(e,s,a){let c;if("string"==typeof e){const t=new URL(e,location.href);c=new n((({url:e})=>e.href===t.href),s,a)}else if(e instanceof RegExp)c=new i(e,s,a);else if("function"==typeof e)c=new n(e,s,a);else{if(!(e instanceof n))throw new t("unsupported-route-type",{moduleName:"workbox-routing",funcName:"registerRoute",paramName:"capture"});c=e}(o||(o=new r,o.addFetchListener(),o.addCacheListener()),o).registerRoute(c)}(new C(s,e))}var O;importScripts("/wp-content/themes/knife/assets/vendor/workbox-sw.js"),self.addEventListener("message",(e=>{e.data&&"SKIP_WAITING"===e.data.type&&self.skipWaiting()})),O={},function(e){L().precache(e)}([{url:"/wp-content/themes/knife/assets/scripts.min.js",revision:"5c365a7f11b54e333d0b7693c83ace7e"},{url:"/wp-content/themes/knife/assets/styles.min.css",revision:"0410eb87df6de3436ca3a6f86b8ee095"},{url:"/wp-content/themes/knife/assets/images/icon-180.png",revision:"ca6f60ad202bcf0ff34672fbe20ac717"},{url:"/wp-content/themes/knife/assets/images/icon-192.png",revision:"b517b08fbd5ca60a03350a5b964c42d9"},{url:"/wp-content/themes/knife/assets/images/icon-32.png",revision:"f56a8a9eb1b6e8058a69b8e4e45104b9"},{url:"/wp-content/themes/knife/assets/images/icon-512.png",revision:"b8c383abf1819d716df6d2d61cb646fc"},{url:"/wp-content/themes/knife/assets/images/logo-feature.png",revision:"e299f2e126d785e2271666d6ad22df97"},{url:"/wp-content/themes/knife/assets/images/logo-title.png",revision:"bd28711698052fb0643f369b67f3a4e5"},{url:"/wp-content/themes/knife/assets/images/logo-white.svg",revision:"4f53ba12405ee6ada5caa5a3a93ecdcd"},{url:"/wp-content/themes/knife/assets/images/logo.svg",revision:"f0ed9b8b7183617d2077179806a2d2b8"},{url:"/wp-content/themes/knife/assets/images/no-avatar.png",revision:"0bca52afdb2b9998132355d716390c9f"},{url:"/wp-content/themes/knife/assets/images/poster-default.png",revision:"65c88948ced61d237f8c4375afbda558"},{url:"/wp-content/themes/knife/assets/images/poster-error.jpg",revision:"b7d4f0bf830d8591c77492e7a802907d"},{url:"/wp-content/themes/knife/assets/images/poster-feature.png",revision:"4453c2c69ec8ca5b76c13a20cceedc38"},{url:"/wp-content/themes/knife/assets/images/vimeo-button.svg",revision:"caa6b9c740d25d59e16586ece1f8c0eb"},{url:"/wp-content/themes/knife/assets/images/youtube-button.svg",revision:"2c8f4c8b8b089f531e3302fff2a98f23"},{url:"/wp-content/themes/knife/assets/vendor/workbox-sw.js",revision:"3a9160b09bb12b5764986598625d6127"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.ttf",revision:"cd775d572ac01516e88b8bd7dd09ba7f"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.woff",revision:"7f90f62e636d90856e6441ba18f249c4"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.woff2",revision:"4951e59d46f15170d92c58f67b075df8"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.ttf",revision:"8e3119bcda3355a873421dbaddecdf52"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff",revision:"b56c494cecd6d3f50cc8c7457fe0fa8d"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff2",revision:"386f0d7694b384bec28e220d3a69e46e"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.ttf",revision:"6285943c4a56190f0ff0082a718c1bd2"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff",revision:"002013ce6ed73928450bd74399f4a47a"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff2",revision:"51c999e9247b3e5be0323758fdea4274"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.ttf",revision:"538855ff31421f8097253d552c4fae23"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff",revision:"5de463e6f69d975ddb7285d25ba1e525"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff2",revision:"4b993072d29d3f80cd87c600a8f05f7b"},{url:"/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.ttf",revision:"1582e4b0243ef677628c142e8a3c3ccb"},{url:"/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.woff",revision:"ad84eab4530c63fd97e0e0c1780da646"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.ttf",revision:"b6370fc7b7e55f25ffc65fd4296d70f0"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff",revision:"c051dfa9b4f64d6e76fedb38070efb11"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff2",revision:"fe63e0a9e535e2993950c861a5f87bf9"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.ttf",revision:"6f861db424bf0bc772ab2f731d23c7b2"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff",revision:"406e5c96996854a749c7cade6ff113fa"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff2",revision:"027228c97f8187ee56f55e9bcbaf59e7"}]),E(O);

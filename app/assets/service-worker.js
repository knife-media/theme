try{self["workbox:core:5.1.4"]&&_()}catch(e){}const e={googleAnalytics:"googleAnalytics",precache:"precache-v2",prefix:"workbox",runtime:"runtime",suffix:"undefined"!=typeof registration?registration.scope:""},t=t=>[e.prefix,t,e.suffix].filter(e=>e&&e.length>0).join("-"),s=s=>s||t(e.precache),n=e=>new URL(String(e),location.href).href.replace(new RegExp("^"+location.origin),""),i=(e,...t)=>{let s=e;return t.length>0&&(s+=" :: "+JSON.stringify(t)),s};class o extends Error{constructor(e,t){super(i(e,t)),this.name=e,this.details=t}}const r=new Set;const a=(e,t)=>e.filter(e=>t in e),c=async({request:e,mode:t,plugins:s=[]})=>{const n=a(s,"cacheKeyWillBeUsed");let i=e;for(const e of n)i=await e.cacheKeyWillBeUsed.call(e,{mode:t,request:i}),"string"==typeof i&&(i=new Request(i));return i},f=async({cacheName:e,request:t,event:s,matchOptions:n,plugins:i=[]})=>{const o=await self.caches.open(e),r=await c({plugins:i,request:t,mode:"read"});let a=await o.match(r,n);for(const t of i)if("cachedResponseWillBeUsed"in t){const i=t.cachedResponseWillBeUsed;a=await i.call(t,{cacheName:e,event:s,matchOptions:n,cachedResponse:a,request:r})}return a},l=async({cacheName:e,request:t,response:s,event:i,plugins:l=[],matchOptions:u})=>{const d=await c({plugins:l,request:t,mode:"write"});if(!s)throw new o("cache-put-with-no-response",{url:n(d.url)});const h=await(async({request:e,response:t,event:s,plugins:n=[]})=>{let i=t,o=!1;for(const t of n)if("cacheWillUpdate"in t){o=!0;const n=t.cacheWillUpdate;if(i=await n.call(t,{request:e,response:i,event:s}),!i)break}return o||(i=i&&200===i.status?i:void 0),i||null})({event:i,plugins:l,response:s,request:d});if(!h)return;const w=await self.caches.open(e),p=a(l,"cacheDidUpdate"),m=p.length>0?await f({cacheName:e,matchOptions:u,request:d}):null;try{await w.put(d,h)}catch(e){throw"QuotaExceededError"===e.name&&await async function(){for(const e of r)await e()}(),e}for(const t of p)await t.cacheDidUpdate.call(t,{cacheName:e,event:i,oldResponse:m,newResponse:h,request:d})},u=async({request:e,fetchOptions:t,event:s,plugins:n=[]})=>{if("string"==typeof e&&(e=new Request(e)),s instanceof FetchEvent&&s.preloadResponse){const e=await s.preloadResponse;if(e)return e}const i=a(n,"fetchDidFail"),r=i.length>0?e.clone():null;try{for(const t of n)if("requestWillFetch"in t){const n=t.requestWillFetch,i=e.clone();e=await n.call(t,{request:i,event:s})}}catch(e){throw new o("plugin-error-request-will-fetch",{thrownError:e})}const c=e.clone();try{let i;i="navigate"===e.mode?await fetch(e):await fetch(e,t);for(const e of n)"fetchDidSucceed"in e&&(i=await e.fetchDidSucceed.call(e,{event:s,request:c,response:i}));return i}catch(e){for(const t of i)await t.fetchDidFail.call(t,{error:e,event:s,originalRequest:r.clone(),request:c.clone()});throw e}};let d;async function h(e,t){const s=e.clone(),n={headers:new Headers(s.headers),status:s.status,statusText:s.statusText},i=t?t(n):n,o=function(){if(void 0===d){const e=new Response("");if("body"in e)try{new Response(e.body),d=!0}catch(e){d=!1}d=!1}return d}()?s.body:await s.blob();return new Response(o,i)}try{self["workbox:precaching:5.1.4"]&&_()}catch(e){}function w(e){if(!e)throw new o("add-to-cache-list-unexpected-type",{entry:e});if("string"==typeof e){const t=new URL(e,location.href);return{cacheKey:t.href,url:t.href}}const{revision:t,url:s}=e;if(!s)throw new o("add-to-cache-list-unexpected-type",{entry:e});if(!t){const e=new URL(s,location.href);return{cacheKey:e.href,url:e.href}}const n=new URL(s,location.href),i=new URL(s,location.href);return n.searchParams.set("__WB_REVISION__",t),{cacheKey:n.href,url:i.href}}class p{constructor(e){this.t=s(e),this.s=new Map,this.i=new Map,this.o=new Map}addToCacheList(e){const t=[];for(const s of e){"string"==typeof s?t.push(s):s&&void 0===s.revision&&t.push(s.url);const{cacheKey:e,url:n}=w(s),i="string"!=typeof s&&s.revision?"reload":"default";if(this.s.has(n)&&this.s.get(n)!==e)throw new o("add-to-cache-list-conflicting-entries",{firstEntry:this.s.get(n),secondEntry:e});if("string"!=typeof s&&s.integrity){if(this.o.has(e)&&this.o.get(e)!==s.integrity)throw new o("add-to-cache-list-conflicting-integrities",{url:n});this.o.set(e,s.integrity)}if(this.s.set(n,e),this.i.set(n,i),t.length>0){const e=`Workbox is precaching URLs without revision info: ${t.join(", ")}\nThis is generally NOT safe. Learn more at https://bit.ly/wb-precache`;console.warn(e)}}}async install({event:e,plugins:t}={}){const s=[],n=[],i=await self.caches.open(this.t),o=await i.keys(),r=new Set(o.map(e=>e.url));for(const[e,t]of this.s)r.has(t)?n.push(e):s.push({cacheKey:t,url:e});const a=s.map(({cacheKey:s,url:n})=>{const i=this.o.get(s),o=this.i.get(n);return this.l({cacheKey:s,cacheMode:o,event:e,integrity:i,plugins:t,url:n})});await Promise.all(a);return{updatedURLs:s.map(e=>e.url),notUpdatedURLs:n}}async activate(){const e=await self.caches.open(this.t),t=await e.keys(),s=new Set(this.s.values()),n=[];for(const i of t)s.has(i.url)||(await e.delete(i),n.push(i.url));return{deletedURLs:n}}async l({cacheKey:e,url:t,cacheMode:s,event:n,plugins:i,integrity:r}){const a=new Request(t,{integrity:r,cache:s,credentials:"same-origin"});let c,f=await u({event:n,plugins:i,request:a});for(const e of i||[])"cacheWillUpdate"in e&&(c=e);if(!(c?await c.cacheWillUpdate({event:n,request:a,response:f}):f.status<400))throw new o("bad-precaching-response",{url:t,status:f.status});f.redirected&&(f=await h(f)),await l({event:n,plugins:i,response:f,request:e===t?a:new Request(e),cacheName:this.t,matchOptions:{ignoreSearch:!0}})}getURLsToCacheKeys(){return this.s}getCachedURLs(){return[...this.s.keys()]}getCacheKeyForURL(e){const t=new URL(e,location.href);return this.s.get(t.href)}async matchPrecache(e){const t=e instanceof Request?e.url:e,s=this.getCacheKeyForURL(t);if(s){return(await self.caches.open(this.t)).match(s)}}createHandler(e=!0){return async({request:t})=>{try{const e=await this.matchPrecache(t);if(e)return e;throw new o("missing-precache-entry",{cacheName:this.t,url:t instanceof Request?t.url:t})}catch(s){if(e)return fetch(t);throw s}}}createHandlerBoundToURL(e,t=!0){if(!this.getCacheKeyForURL(e))throw new o("non-precached-url",{url:e});const s=this.createHandler(t),n=new Request(e);return()=>s({request:n})}}let m;const b=()=>(m||(m=new p),m);const g=(e,t)=>{const s=b().getURLsToCacheKeys();for(const n of function*(e,{ignoreURLParametersMatching:t,directoryIndex:s,cleanURLs:n,urlManipulation:i}={}){const o=new URL(e,location.href);o.hash="",yield o.href;const r=function(e,t=[]){for(const s of[...e.searchParams.keys()])t.some(e=>e.test(s))&&e.searchParams.delete(s);return e}(o,t);if(yield r.href,s&&r.pathname.endsWith("/")){const e=new URL(r.href);e.pathname+=s,yield e.href}if(n){const e=new URL(r.href);e.pathname+=".html",yield e.href}if(i){const e=i({url:o});for(const t of e)yield t.href}}(e,t)){const e=s.get(n);if(e)return e}};let v=!1;function y(e){v||((({ignoreURLParametersMatching:e=[/^utm_/],directoryIndex:t="index.html",cleanURLs:n=!0,urlManipulation:i}={})=>{const o=s();self.addEventListener("fetch",s=>{const r=g(s.request.url,{cleanURLs:n,directoryIndex:t,ignoreURLParametersMatching:e,urlManipulation:i});if(!r)return;let a=self.caches.open(o).then(e=>e.match(r)).then(e=>e||fetch(r));s.respondWith(a)})})(e),v=!0)}const k=[],R={get:()=>k,add(e){k.push(...e)}},q=e=>{const t=b(),s=R.get();e.waitUntil(t.install({event:e,plugins:s}).catch(e=>{throw e}))},U=e=>{const t=b();e.waitUntil(t.activate())};var L;importScripts("/wp-content/themes/knife/assets/vendor/workbox-sw.js"),self.addEventListener("message",e=>{e.data&&"SKIP_WAITING"===e.data.type&&self.skipWaiting()}),L={},function(e){b().addToCacheList(e),e.length>0&&(self.addEventListener("install",q),self.addEventListener("activate",U))}([{url:"/wp-content/themes/knife/assets/scripts.min.js",revision:"2df5a7abc9276983398cd9e4610cd4a3"},{url:"/wp-content/themes/knife/assets/styles.min.css",revision:"58321206aeedbb7bbcd3ec3bcfc61477"},{url:"/wp-content/themes/knife/assets/images/cents-header.jpg",revision:"e7f7b4a45d3df616cf4c07e25addf419"},{url:"/wp-content/themes/knife/assets/images/icon-180.png",revision:"ca6f60ad202bcf0ff34672fbe20ac717"},{url:"/wp-content/themes/knife/assets/images/icon-192.png",revision:"b517b08fbd5ca60a03350a5b964c42d9"},{url:"/wp-content/themes/knife/assets/images/icon-32.png",revision:"f56a8a9eb1b6e8058a69b8e4e45104b9"},{url:"/wp-content/themes/knife/assets/images/icon-512.png",revision:"b8c383abf1819d716df6d2d61cb646fc"},{url:"/wp-content/themes/knife/assets/images/logo-feature.png",revision:"e299f2e126d785e2271666d6ad22df97"},{url:"/wp-content/themes/knife/assets/images/logo-title.png",revision:"bd28711698052fb0643f369b67f3a4e5"},{url:"/wp-content/themes/knife/assets/images/logo-white.svg",revision:"4f53ba12405ee6ada5caa5a3a93ecdcd"},{url:"/wp-content/themes/knife/assets/images/logo.svg",revision:"f0ed9b8b7183617d2077179806a2d2b8"},{url:"/wp-content/themes/knife/assets/images/no-avatar.png",revision:"0bca52afdb2b9998132355d716390c9f"},{url:"/wp-content/themes/knife/assets/images/poster-default.png",revision:"65c88948ced61d237f8c4375afbda558"},{url:"/wp-content/themes/knife/assets/images/poster-error.jpg",revision:"b7d4f0bf830d8591c77492e7a802907d"},{url:"/wp-content/themes/knife/assets/images/poster-feature.png",revision:"4453c2c69ec8ca5b76c13a20cceedc38"},{url:"/wp-content/themes/knife/assets/images/vimeo-button.svg",revision:"caa6b9c740d25d59e16586ece1f8c0eb"},{url:"/wp-content/themes/knife/assets/images/youtube-button.svg",revision:"2c8f4c8b8b089f531e3302fff2a98f23"},{url:"/wp-content/themes/knife/assets/vendor/glide.min.js",revision:"8adeb654f20f19f53cb39764a6199126"},{url:"/wp-content/themes/knife/assets/vendor/workbox-sw.js",revision:"3a9160b09bb12b5764986598625d6127"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.ttf",revision:"cd775d572ac01516e88b8bd7dd09ba7f"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.woff",revision:"7f90f62e636d90856e6441ba18f249c4"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-black.woff2",revision:"4951e59d46f15170d92c58f67b075df8"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.ttf",revision:"8e3119bcda3355a873421dbaddecdf52"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff",revision:"b56c494cecd6d3f50cc8c7457fe0fa8d"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff2",revision:"386f0d7694b384bec28e220d3a69e46e"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.ttf",revision:"6285943c4a56190f0ff0082a718c1bd2"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff",revision:"002013ce6ed73928450bd74399f4a47a"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff2",revision:"51c999e9247b3e5be0323758fdea4274"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.ttf",revision:"538855ff31421f8097253d552c4fae23"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff",revision:"5de463e6f69d975ddb7285d25ba1e525"},{url:"/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff2",revision:"4b993072d29d3f80cd87c600a8f05f7b"},{url:"/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.ttf",revision:"c5e910722e645f0eb252f3b065a9272d"},{url:"/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.woff",revision:"ff4b26577114a14251dfe61a6fd23ed1"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.ttf",revision:"b6370fc7b7e55f25ffc65fd4296d70f0"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff",revision:"c051dfa9b4f64d6e76fedb38070efb11"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff2",revision:"fe63e0a9e535e2993950c861a5f87bf9"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.ttf",revision:"6f861db424bf0bc772ab2f731d23c7b2"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff",revision:"406e5c96996854a749c7cade6ff113fa"},{url:"/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff2",revision:"027228c97f8187ee56f55e9bcbaf59e7"}]),y(L);

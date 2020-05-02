/**
 * Welcome to your Workbox-powered service worker!
 *
 * You'll need to register this file in your web app and you should
 * disable HTTP caching for this file too.
 * See https://goo.gl/nhQhGp
 *
 * The rest of the code is auto-generated. Please don't update this file
 * directly; instead, make changes to your Workbox build configuration
 * and re-run your build process.
 * See https://goo.gl/2aRDsh
 */

importScripts(
  "/wp-content/themes/knife/assets/vendor/workbox-sw.js"
);

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

/**
 * The workboxSW.precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */
self.__precacheManifest = [
  {
    "url": "/wp-content/themes/knife/assets/scripts.min.js",
    "revision": "6ede6f624bae13644d1db6f194cc4353"
  },
  {
    "url": "/wp-content/themes/knife/assets/styles.min.css",
    "revision": "723d46189d241c42174161f7bf367fce"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/header-cents.jpg",
    "revision": "8ccc0a19a49d4f247e6362d5319eb761"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/icon-180.png",
    "revision": "ca6f60ad202bcf0ff34672fbe20ac717"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/icon-192.png",
    "revision": "b517b08fbd5ca60a03350a5b964c42d9"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/icon-32.png",
    "revision": "f56a8a9eb1b6e8058a69b8e4e45104b9"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/icon-512.png",
    "revision": "b8c383abf1819d716df6d2d61cb646fc"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/logo-feature.png",
    "revision": "e299f2e126d785e2271666d6ad22df97"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/logo-title.png",
    "revision": "bd28711698052fb0643f369b67f3a4e5"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/logo-white.svg",
    "revision": "4f53ba12405ee6ada5caa5a3a93ecdcd"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/logo.svg",
    "revision": "f0ed9b8b7183617d2077179806a2d2b8"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/poster-default.png",
    "revision": "65c88948ced61d237f8c4375afbda558"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/poster-error.jpg",
    "revision": "b7d4f0bf830d8591c77492e7a802907d"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/poster-feature.png",
    "revision": "4453c2c69ec8ca5b76c13a20cceedc38"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/vimeo-button.svg",
    "revision": "caa6b9c740d25d59e16586ece1f8c0eb"
  },
  {
    "url": "/wp-content/themes/knife/assets/images/youtube-button.svg",
    "revision": "2c8f4c8b8b089f531e3302fff2a98f23"
  },
  {
    "url": "/wp-content/themes/knife/assets/vendor/glide.min.js",
    "revision": "8adeb654f20f19f53cb39764a6199126"
  },
  {
    "url": "/wp-content/themes/knife/assets/vendor/workbox-sw.js",
    "revision": "6e1e47d706556eac8524f396e785d4bb"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-black.ttf",
    "revision": "cd775d572ac01516e88b8bd7dd09ba7f"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-black.woff",
    "revision": "d41d8cd98f00b204e9800998ecf8427e"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-black.woff2",
    "revision": "4951e59d46f15170d92c58f67b075df8"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-bold.ttf",
    "revision": "8e3119bcda3355a873421dbaddecdf52"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff",
    "revision": "b56c494cecd6d3f50cc8c7457fe0fa8d"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-bold.woff2",
    "revision": "386f0d7694b384bec28e220d3a69e46e"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-medium.ttf",
    "revision": "6285943c4a56190f0ff0082a718c1bd2"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff",
    "revision": "002013ce6ed73928450bd74399f4a47a"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-medium.woff2",
    "revision": "51c999e9247b3e5be0323758fdea4274"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-regular.ttf",
    "revision": "538855ff31421f8097253d552c4fae23"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff",
    "revision": "5de463e6f69d975ddb7285d25ba1e525"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/formular/formular-regular.woff2",
    "revision": "4b993072d29d3f80cd87c600a8f05f7b"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.ttf",
    "revision": "023e6e622352bceea020304a3006d8fb"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/knife-icons/knife-icons.woff",
    "revision": "c0842fec3d186862e8555e752a51b5d4"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.ttf",
    "revision": "b6370fc7b7e55f25ffc65fd4296d70f0"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff",
    "revision": "c051dfa9b4f64d6e76fedb38070efb11"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-italic.woff2",
    "revision": "fe63e0a9e535e2993950c861a5f87bf9"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.ttf",
    "revision": "6f861db424bf0bc772ab2f731d23c7b2"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff",
    "revision": "406e5c96996854a749c7cade6ff113fa"
  },
  {
    "url": "/wp-content/themes/knife/assets/fonts/ptserif/ptserif-regular.woff2",
    "revision": "027228c97f8187ee56f55e9bcbaf59e7"
  }
].concat(self.__precacheManifest || []);
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});

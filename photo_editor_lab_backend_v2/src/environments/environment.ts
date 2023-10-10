/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : environment.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:54:31 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */



// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  apiUrls: {
    // BASE_URL: 'http://192.168.0.116/photo_editor_lab_backend/api/public/api/',
    // BASE_URL: 'http://192.168.0.105/photo_editor_lab_backend/api/public/api/',
    BASE_URL: 'http://192.168.0.110/photo_editor_lab_backend/api/public/api/',
    // BASE_URL: 'http://192.168.0.104/photo_editor_lab_backend/api/public/api/',
    // BASE_URL: 'https://local104.ngrok.io/photo_editor_lab_backend/api/public/api/',
    // BASE_URL: 'https://videoflyer.ngrok.io/photo_editor_lab_backend/api/public/api/',
    FONT_CATEGORY_ID: 5
  },
  // CATAGORY_IDS: {
  //   FRAME: 1,
  //   STICKER: 2,
  //   BACKGROUND: 3,
  //   TEMPLATES: 4,
  //   TEXT: 5,
  //   THREED_OBJECTS: 6,
  //   FONTS: 7,
  //   VIDEO: 8,
  //   AUDIO: 9,
  //   INTRO: 10
  // },
  // CONTENT_TYPES: {
  //   IMAGE: 1,
  //   VIDEO: 2,
  //   AUDIO: 3,
  //   CARD_JSON: 4,
  //   TEXT_JSON: 5,
  //   THREED_TEXT_JSON: 6,
  //   THREED_SHAPE: 7,
  //   SVG: 8,
  //   VIDEO_JSON: 9,
  //   INTRO_JSON: 10,
  //   INTRO: 11
  // },
};

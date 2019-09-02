// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  apiUrls: {
    BASE_URL: 'http://192.168.0.113/photo_editor_lab_backend/api/public/api/',
    FONT_CATEGORY_ID: 5
  }
};

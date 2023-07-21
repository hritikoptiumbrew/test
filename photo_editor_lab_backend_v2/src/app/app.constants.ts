/*
 * Optimumbrew Technology

 * Project       : Photo Editor Lab
 * File          : app.constants.ts
 * File Created  : Thursday, 15th October 2020 01:12:03 pm
 * Author        : Optimumbrew
 * Auther Email  : info@optimumbrew.com
 * Last Modified : Thursday, 29th October 2020 10:58:58 am
 * -----
 * Copyright 2018 - 2020 Optimumbrew Technology
 */


import { environment } from '../environments/environment';

export const HOST = environment;

// export const CATAGORY_IDS = HOST.CATAGORY_IDS;
// export const CONTENT_TYPES = HOST.CONTENT_TYPES;

export const ENV_CONFIG = Object.freeze({ "development": true, ENABLE_2FA: true, });

export const ERROR = Object.freeze({
    OFFLINE: 'Unable to connect with server, please try again.',
    EMPTY_EMAIL: 'Please enter email',
    INVALID_EMAIL: 'Please enter valid email',
    EMPTY_PASSWORD: 'Please enter password',
    INVALID_PASSWORD: 'Password length must be between 8 to 50 character',
    INVALIDCHANGE_PASS: 'Password should be atleast 6 character',
    EMPTY_FIRSTNAME: 'Please enter name',
    INVALID_FIRSTNAME: 'Please enter valid name',
    SERVER_ERR: 'Unable to connect with server, please reload the page.',
    SERVER_INTERNET_ERR: 'Unable to connect with server, please check your internet connection.',
    CURR_PASSWORD: 'Please enter your current password',
    OLD_PASSWORD: 'Please enter your old password',
    NEW_PASSWORD: 'Please enter your new password',
    RE_PASSWORD: 'Please re-enter your new password',
    MISSMATCH_PASSWORD: "New password & re-enter password doesn't match",
    PASS_SERVER_ERR: "Unable to change password, please try again.",
    CAT_NAME_EMPTY: "Please enter category name",
    CAT_LOG_EMPTY: "Please enter catalog name",
    SUB_CAT_NAME_EMPTY: "Please enter sub category name",
    ALPHA_VALID: "Alphanumeric,space and special charcter like '(_),(-),(#),(&)' are allowed",
    ALPHA_NUM_VALID: "Only alpha space and number allow",
    TAG_EMPTY_NAME: "Please enter tag name",
    NAME_REQ: "Name is required",
    IMG_REQ: "Image is required",
    TITLE_EMPTY: "Please enter title",
    CONTENT_EMPTY: "Please enter content",
    SEARCH_QUERY_EMPTY: "Please enter search query",
    SEL_CATA_PRICE_TYPE: "Please select catalog price type",
    SEL_CATE_TYPE: "Please select sub-category type",
    SEL_CATA_TYPE: "Please select catalog type",
    SEL_TEMP_PRICE_TYPE: "Please select template price type",
    SEL_TEMP_TYPE: "Please select template type",
    SEL_ORIENTATION_TYPE: "Please select orientation type",
    SEL_OBJ_FILE: "STL Object file is required",
    SEL_CONTENT_TYPE: "Please select content type",
    MUL_IMG_EMPTY: "Please select one or multiple images",
    SEARCT_TAG_EMPTY: "Please Select/Enter atleast one search tag",
    JSON_DATA_MPTY: "Please enter JSON data",
    SEL_NEW_IMG: "Please select new image",
    SAMPLE_IMG_EMPTY: "Please select sample image",
    DISPLAY_IMG_EMPTY: "Please select display image",
    IMG_TYPE_EMPTY: "Please select sample type",
    U_SEARCH_TYPE_EMPTY: "Please select search type",
    U_SEARCH_QUERY_EMPTY: "Please select search type",
    REDIS_DEL_EMPTY: "Please select atleast one key to delete",
    ADV_BANNER_EMPTY: "Please select advertisement banner",
    APP_LOGO_EMPTY: "Please select application logo",
    APP_NAME_EMPTY: "Please enter application name",
    APP_URL_EMPTY: "Please enter application url",
    APP_DESC_EMPTY: "Please enter application description",
    UNDER_DEV: "This module is under development",
    NOTIF_TITLE_EMPTY: "Please enter notification title",
    NOTIF_DESC_EMPTY: "Please enter notification description",
    INVALID_JSON: "Invalid JSON, Please correct the JSON and try again.",
    LOGGED_OUT_DIFF_TAB: "You have been logged out, please login again.",
    VERIFICATION_CODE_EMPTY: "Please enter verification code",
    STK_ALREADY_EXISTS: "STL file already exists, please click on replace button to replace the file.",
    TWO_FA_DIS_SUCCESS: "Two-factor Authentication disabled successfully",
    EMPTY_FONT_FILE: 'Please choose font file to upload',
    EMPTY_FONT_NAME: 'Please enter font name',
    EMPTY_FONT_PATH: 'Please enter font path',
    SEL_TMPLT_MV: "Please select a catalog to move the template.",
    IMG_UP_EMPTY: "Please select one or multiple images",
    IMG_REP_EMPTY: "Please select one or multiple images to replace",
    JSON_DATA_EMPTY: "Please enter JSON data or select file",
    FONT_FILE_EMPTY: "Please choose font file to upload",
    FONT_NAME_EMPTY: "Please enter font name",
    FONT_PATH_EMPTY: "Please enter font path",
    SEL_CACHE_KEYS: "Please select cache keys to delete",
    SEL_FONT: "Please select fonts which you want to remove",
    SERVER_URL_EMPTY: "Please  enter server url",
    VALIDATION_SEL_CAT: "Please select category",
    VALIDATION_NAME_EMPTY: "Please enter validation name",
    VALIDATION_VALUE_EMPTY: "Please enter validation Value",
    VALIDATION_DESCRIP_EMPTY: "Please enter description",
    SUB_CAT_NAME_SEARCH_EMPTY: "Please enter sub category name to search",
    CAT_LOG_SEARCH_EMPTY: "Please enter catalog name to search",
    NOT_SUPPORT_MULTIPAGE: "Any one sub category is not supporting multipage"
});
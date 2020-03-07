define({ "api": [
  {
    "type": "post",
    "url": "getBlogContentByIdForUser",
    "title": "getBlogContentByIdForUser",
    "name": "getBlogContentByIdForUser",
    "group": "1User_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"blog_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content fetched successfully.\",\n\"cause\": \"\",\n\"data\": [\n{\n\"blog_id\": 23,\n\"thumbnail_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d26d844c218c_blog_image_1562826820.jpg\",\n\"compressed_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d26d844c218c_blog_image_1562826820.jpg\",\n\"original_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/original/5d26d844c218c_blog_image_1562826820.jpg\",\n\"webp_original_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d26d844c218c_blog_image_1562826820.webp\",\n\"webp_thumbnail_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d26d844c218c_blog_image_1562826820.webp\",\n\"height\": 205,\n\"width\": 460,\n\"title\": \"{\\\"text_color\\\":\\\"#ff8040\\\",\\\"text_size\\\":16,\\\"text_value\\\":\\\"CONTENT OF THE WEEK | JUNE 28, 2019 \\\"}\",\n\"subtitle\": \"{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":36,\\\"text_value\\\":\\\"Share the love with the Beth Ellen font\\\"}\",\n\"blog_json\": \"{\\\"title\\\":{\\\"text_color\\\":\\\"#ff8040\\\",\\\"text_size\\\":16,\\\"text_value\\\":\\\"CONTENT OF THE WEEK | JUNE 28, 2019 \\\"},\\\"subtitle\\\":{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":36,\\\"text_value\\\":\\\"Share the love with the Beth Ellen font\\\"},\\\"blog_data\\\":\\\"<div class=\\\\\\\"blog-content-container w-container\\\\\\\" style=\\\\\\\"margin-left: auto; margin-right: auto; max-width: 820px; margin-bottom: 0px; padding-right: 48px; padding-left: 48px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\\\\\"><div class=\\\\\\\"blog-rich-text w-richtext\\\\\\\" style=\\\\\\\"max-width: none; margin-bottom: 0px;\\\\\\\"><p data-w-id=\\\\\\\"22c9a481-8fcd-41e6-58b6-2557f5185c67\\\\\\\" style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\"><span data-w-id=\\\\\\\"ca7b771b-5085-17f2-9436-55a954d2d2d1\\\\\\\" style=\\\\\\\"font-weight: 700;\\\\\\\"><span data-w-id=\\\\\\\"256615da-5297-05cc-1fcc-5d117d06336c\\\\\\\" style=\\\\\\\"color: rgb(255, 207, 0);\\\\\\\">At Over HQ, we feel so blessed<\\\\/span><\\\\/span>&nbsp;to have such a wildly talented community. People who find beauty in the darkness, and inspire the world by sharing their own unique story. &nbsp;<br data-w-id=\\\\\\\"4146cb54-c93e-83f0-fd6c-72864595f2cc\\\\\\\"><\\\\/p><p data-w-id=\\\\\\\"83868078-48d0-7ddd-a395-b8918c02f15b\\\\\\\" style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\">That's exactly what Over Artist&nbsp;<a data-w-id=\\\\\\\"500bd531-3a4f-03ad-76ef-84f1bee4174b\\\\\\\" href=\\\\\\\"http:\\\\/\\\\/robjelinskistudios.com\\\\/\\\\\\\" data-rt-link-type=\\\\\\\"external\\\\\\\" style=\\\\\\\"color: rgb(72, 66, 67); text-decoration: none;\\\\\\\">Rob Jelinski<\\\\/a>&nbsp;did when he created a font in his mom\\\\u2019s handwriting, to honor her after she passed away. &nbsp;<br data-w-id=\\\\\\\"09eb3989-c1c0-792b-9349-0838955e7c37\\\\\\\"><\\\\/p><p data-w-id=\\\\\\\"7018f834-79c6-438c-c240-257118722874\\\\\\\" style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\">As an artist and graphic designer, Rob wanted to share the Beth Ellen font with the world and bring it to life in the Over app, so that his mom's memory would live on forever.&nbsp;<br data-w-id=\\\\\\\"088ad354-17e6-b285-ee43-98bcffc5e39c\\\\\\\"><\\\\/p><blockquote data-w-id=\\\\\\\"2d4c6bf4-047c-4a55-edda-f269f3c34729\\\\\\\" style=\\\\\\\"margin-top: 36px; margin-bottom: 36px; color: rgb(86, 79, 80); max-width: 720px; padding: 0px 20px 0px 36px; border-left: 5px solid rgb(255, 207, 0); font-size: 36px; line-height: 54px;\\\\\\\">My single request is that you help the legacy of Beth Ellen live on by sending a short note to someone you love each time the font is used.&nbsp;<br data-w-id=\\\\\\\"b2a8b15a-fa4f-f616-96f4-ca0d488dc733\\\\\\\"><\\\\/blockquote><p data-w-id=\\\\\\\"a4c898d5-f34e-9c95-e7e7-2499f5cb1dfb\\\\\\\" style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\">So send a little love to someone today! \\\\ud83d\\\\udc95<br data-w-id=\\\\\\\"75a95d8d-1347-aa7b-20ae-0b776eee9545\\\\\\\"><\\\\/p><p data-w-id=\\\\\\\"92163f65-699a-31ab-11be-0896ed7dc813\\\\\\\" style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\">And if you need some help getting started, here are our some of our fave heartfelt quotes expressing love and gratitude \\\\u2013 all featuring the Beth Ellen font. &nbsp;<\\\\/p><figure data-w-id=\\\\\\\"a1158060-9b50-4df6-cc9d-4591ec52ac51\\\\\\\" class=\\\\\\\"w-richtext-figure-type-image w-richtext-align-normal\\\\\\\" data-rt-type=\\\\\\\"image\\\\\\\" data-rt-align=\\\\\\\"normal\\\\\\\" style=\\\\\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\\\\\"><div data-w-id=\\\\\\\"2776b881-5753-2b87-b01b-6c8c37b508ea\\\\\\\" style=\\\\\\\"font-size: 0px; color: transparent; display: inline-block;\\\\\\\"><img data-w-id=\\\\\\\"b33107d4-8e06-99cf-ca9d-7213caa5063e\\\\\\\" src=\\\\\\\"https:\\\\/\\\\/assets-global.website-files.com\\\\/5c7e6a9e0899242e0da98296\\\\/5d16291ad639969cb99c193f_IMG_0517.jpeg\\\\\\\" style=\\\\\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\\\\\"><\\\\/div><\\\\/figure><figure data-w-id=\\\\\\\"41be35ee-2ca1-c46d-cedf-41886418990f\\\\\\\" class=\\\\\\\"w-richtext-figure-type-image w-richtext-align-normal\\\\\\\" data-rt-type=\\\\\\\"image\\\\\\\" data-rt-align=\\\\\\\"normal\\\\\\\" style=\\\\\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\\\\\"><div data-w-id=\\\\\\\"7c31acb5-c799-708f-3314-8128518151b2\\\\\\\" style=\\\\\\\"font-size: 0px; color: transparent; display: inline-block;\\\\\\\"><img data-w-id=\\\\\\\"b185eae1-43a8-a3d0-556e-e5d9d0a6853f\\\\\\\" src=\\\\\\\"https:\\\\/\\\\/assets-global.website-files.com\\\\/5c7e6a9e0899242e0da98296\\\\/5d1629451cd58642f6b91e1c_IMG_0518.jpeg\\\\\\\" style=\\\\\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\\\\\"><\\\\/div><\\\\/figure><figure data-w-id=\\\\\\\"52d8388f-2f50-8a4e-54be-0ca3b5d4caba\\\\\\\" class=\\\\\\\"w-richtext-figure-type-image w-richtext-align-normal\\\\\\\" data-rt-type=\\\\\\\"image\\\\\\\" data-rt-align=\\\\\\\"normal\\\\\\\" style=\\\\\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\\\\\"><div data-w-id=\\\\\\\"bbb57cdd-c599-5c81-b991-617a22f8c5df\\\\\\\" style=\\\\\\\"font-size: 0px; color: transparent; display: inline-block;\\\\\\\"><img data-w-id=\\\\\\\"9ba93941-e474-2dfe-55f4-c215647082f4\\\\\\\" src=\\\\\\\"https:\\\\/\\\\/assets-global.website-files.com\\\\/5c7e6a9e0899242e0da98296\\\\/5d16295e34e447164da049b8_IMG_0521.jpeg\\\\\\\" style=\\\\\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\\\\\"><\\\\/div><div data-w-id=\\\\\\\"bbb57cdd-c599-5c81-b991-617a22f8c5df\\\\\\\" style=\\\\\\\"font-size: 0px; color: transparent; display: inline-block;\\\\\\\"><br><\\\\/div><\\\\/figure><figure data-w-id=\\\\\\\"f8c522d5-ce7e-947f-f572-a417b1d20d5f\\\\\\\" class=\\\\\\\"w-richtext-figure-type-image w-richtext-align-normal\\\\\\\" data-rt-type=\\\\\\\"image\\\\\\\" data-rt-align=\\\\\\\"normal\\\\\\\" style=\\\\\\\"display: table; margin-top: 16px; margin-bottom: 16px; position: relative; max-width: 60%; clear: both;\\\\\\\"><div data-w-id=\\\\\\\"99435f16-eacc-8cb8-dd54-ff688026ed3f\\\\\\\" style=\\\\\\\"font-size: 0px; color: transparent; display: inline-block;\\\\\\\"><img data-w-id=\\\\\\\"1640f001-51c6-7f52-e0e9-bdee4cba18eb\\\\\\\" src=\\\\\\\"https:\\\\/\\\\/assets-global.website-files.com\\\\/5c7e6a9e0899242e0da98296\\\\/5d162970d1287ccc9e080bf5_IMG_0523.jpeg\\\\\\\" style=\\\\\\\"border: 0px; max-width: 100%; display: inline-block; width: 434px;\\\\\\\"><\\\\/div><\\\\/figure><\\\\/div><\\\\/div><div class=\\\\\\\"container blog-content-container w-container\\\\\\\" style=\\\\\\\"max-width: 820px; padding-right: 48px; padding-bottom: 0px; padding-left: 48px; -webkit-box-pack: center; justify-content: center; -webkit-box-align: center; align-items: center; margin-bottom: 0px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\\\\\"><div class=\\\\\\\"blog-img-gallery\\\\\\\" style=\\\\\\\"display: flex; margin-top: 32px; margin-bottom: 32px; -webkit-box-pack: center; justify-content: center; -webkit-box-align: start; align-items: flex-start;\\\\\\\"><\\\\/div><\\\\/div><div class=\\\\\\\"blog-content-container w-container\\\\\\\" style=\\\\\\\"margin-left: auto; margin-right: auto; max-width: 820px; margin-bottom: 0px; padding-right: 48px; padding-left: 48px; color: rgb(154, 142, 144); font-family: &quot;Proxima soft&quot;, sans-serif; font-size: 20px;\\\\\\\"><div class=\\\\\\\"blog-rich-text w-richtext\\\\\\\" style=\\\\\\\"max-width: none; margin-bottom: 0px;\\\\\\\"><p style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\"><span style=\\\\\\\"font-weight: 700;\\\\\\\"><span style=\\\\\\\"color: rgb(255, 207, 0);\\\\\\\">Tip<\\\\/span>:<\\\\/span>&nbsp;For a realistic handwritten note effect, try using the Beth Ellen font on textured paper as a background.<br><\\\\/p><p style=\\\\\\\"margin-bottom: 32px; color: rgb(103, 94, 96); line-height: 32px;\\\\\\\">\\\\u200d<\\\\/p><div><br><\\\\/div><\\\\/div><button class=\\\\\\\"sttc-button\\\\\\\" onclick=\\\\\\\"searchTemplate('illustration')\\\\\\\">Search Template<\\\\/button><br><\\\\/div><style>.sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, \\\\\\\"Segoe UI\\\\\\\", Roboto, \\\\\\\"Helvetica Neue\\\\\\\", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}<\\\\/style><script>function OpenTemplate(templateID) { alert(\\\\\\\"OpenTemplate\\\\\\\", templateID); } function searchTemplate(searchTag) { alert(\\\\\\\"searchTemplate\\\\\\\", searchTag); } <\\\\/script>\\\",\\\"fg_image\\\":\\\"5d1b4e68c98e2_blog_image_1562070632.jpg\\\"}\",\n\"is_active\": 1\n}\n]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "1User_Blog_content"
  },
  {
    "type": "post",
    "url": "getBlogContentByUser",
    "title": "getBlogContentByUser",
    "name": "getBlogContentByUser",
    "group": "1User_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1,\n\"item_count\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 3,\n\"is_next_page\": false,\n\"result\": [\n{\n\"blog_id\": 6,\n\"fg_image\": \"5d13082801518_blog_image_1561528360.png\",\n\"thumbnail_img\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/thumbnail/5d13082801518_blog_image_1561528360.png\",\n\"compressed_img\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5d13082801518_blog_image_1561528360.png\",\n\"original_img\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/original/5d13082801518_blog_image_1561528360.png\",\n\"blog_json\": \"{\\\"title\\\":\\\"test\\\",\\\"subtitle\\\":\\\"demo\\\",\\\"blog_data\\\":\\\"<header class=\\\\\\\"entry-header\\\\\\\" style=\\\\\\\"box-sizing: inherit; margin-bottom: 27px; color: rgb(64, 64, 64); font-family: Lato, sans-serif; font-size: 16px;\\\\\\\"><\\\\/div>\\\"}\",\n\"is_active\": 1\n},\n{\n\"blog_id\": 2,\n\"fg_image\": \"5d13082801518_blog_image_1561528360.png\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5d11e5af99f3f_blog_image_1561453999.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5d11e5af99f3f_blog_image_1561453999.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5d11e5af99f3f_blog_image_1561453999.png\",\n\"blog_json\": \"{\\\"title\\\":\\\"Title Description\\\",\\\"subtitle\\\":\\\"Sub title sub title\\\",\\\"blog_data\\\":\\\"<header class=\\\\\\\"entry-header\\\\\\\" style=\\\\\\\"box-sizing: inherit; margin-bottom: 27px; color: rgb(64, 64, 64); font-family: Lato, sans-serif; font-size: 16px;\\\\\\\"><\\\\/div>\\\"}\",\n\"is_active\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "1User_Blog_content"
  },
  {
    "type": "post",
    "url": "getBlogListByUser",
    "title": "getBlogListByUser",
    "name": "getBlogListByUser",
    "group": "1User_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"platform\"=2,//1=Android,2=IOS\n\"catalog_id\":895,\n\"page\":1,\n\"item_count\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 10,\n\"is_next_page\": true,\n\"result\": [\n{\n\"blog_id\": 27,\n\"thumbnail_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/thumbnail/5d26bff8efadc_blog_image_1562820600.png\",\n\"compressed_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/compressed/5d26bff8efadc_blog_image_1562820600.png\",\n\"original_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/original/5d26bff8efadc_blog_image_1562820600.png\",\n\"webp_original_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/webp_original/5d26bff8efadc_blog_image_1562820600.png\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/webp_thumbnail/5d26bff8efadc_blog_image_1562820600.png\",\n\"height\": 256,\n\"width\": 256,\n\"title\": \"{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":16,\\\"text_value\\\":\\\"test\\\"}\",\n\"subtitle\": \"{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":36,\\\"text_value\\\":\\\"subtitle\\\"}\",\n\"catalog_id\":895,\n\"is_active\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "1User_Blog_content"
  },
  {
    "type": "post",
    "url": "addCategory",
    "title": "addCategory",
    "name": "AddCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"name\":\"Nature\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addSubCategory",
    "title": "addSubCategory",
    "name": "AddSubCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":1, //compulsory\n\"name\":\"Nature\", //compulsory\n\"is_featured\":1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)\n}\nfile:image.jpeg //compulsory",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addBlogContent",
    "title": "addBlogContent",
    "name": "addBlogContent",
    "group": "Admin_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"catalog_id\":895,//compulsory\n\"title\":\"test\", //compulsory\n\"subtitle\":\"demo\", //compulsory\n\"blog_data\":\"<p></p>\" //compulsory\n}\nfile:ob.jpg //compulsory",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "Admin_Blog_content"
  },
  {
    "type": "post",
    "url": "deleteBlogContent",
    "title": "deleteBlogContent",
    "name": "deleteBlogContent",
    "group": "Admin_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"blog_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "Admin_Blog_content"
  },
  {
    "type": "post",
    "url": "getBlogContent",
    "title": "getBlogContent",
    "name": "getBlogContent",
    "group": "Admin_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":895,\n\"page\":1,\n\"item_count\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 9,\n\"is_next_page\": false,\n\"result\": [\n{\n\"blog_id\": 26,\n\"fg_image\": \"5d26bbe50f627_blog_image_1562819557.png\",\n\"thumbnail_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/thumbnail/5d26bbe50f627_blog_image_1562819557.png\",\n\"compressed_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/compressed/5d26bbe50f627_blog_image_1562819557.png\",\n\"original_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/original/5d26bbe50f627_blog_image_1562819557.png\",\n\"webp_original_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/webp_original/5d26bbe50f627_blog_image_1562819557.png\",\n\"webp_thumbnail_img\": \"http://192.168.0.114/videoflyer_backend/image_bucket/webp_thumbnail/5d26bbe50f627_blog_image_1562819557.png\",\n\"title\": \"{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":16,\\\"text_value\\\":\\\"test\\\"}\",\n\"subtitle\": \"{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":36,\\\"text_value\\\":\\\"test\\\"}\",\n\"blog_json\": \"{\\\"title\\\":{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":16,\\\"text_value\\\":\\\"test\\\"},\\\"subtitle\\\":{\\\"text_color\\\":\\\"#000000\\\",\\\"text_size\\\":36,\\\"text_value\\\":\\\"test\\\"},\\\"blog_data\\\":\\\"test<style>.sttc-button {font-family: -apple-system, system-ui, BlinkMacSystemFont, \\\\\\\"Segoe UI\\\\\\\", Roboto, \\\\\\\"Helvetica Neue\\\\\\\", Arial, sans-serif; font-size: 1rem; font-weight: 500; border: none; border-radius: 4px; box-shadow: none; color: #ffffff; cursor: pointer; display: inline-block; margin: 0px; padding: 8px 18px; text-decoration: none; background-color: #ffcd00; overflow-wrap: break-word; user-select:none !important;}.sttc-button:hover, .sttc-button:focus{ background-color: #d9ae00;}<\\\\/style><script>function OpenTemplate(templateID) { alert(\\\\\\\"OpenTemplate - \\\\\\\" + templateID); } function searchTemplate(searchTag) { alert(\\\\\\\"searchTemplate - \\\\\\\" + searchTag); } <\\\\/script>\\\",\\\"fg_image\\\":\\\"5d26bbe50f627_blog_image_1562819557.png\\\"}\",\n\"catalog_id\":895,\n\"is_active\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "Admin_Blog_content"
  },
  {
    "type": "post",
    "url": "setBlogRankOnTheTopByAdmin",
    "title": "setBlogRankOnTheTopByAdmin",
    "name": "setBlogRankOnTheTopByAdmin",
    "group": "Admin_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"blog_id\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content rank set successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "Admin_Blog_content"
  },
  {
    "type": "post",
    "url": "updateBlogContent",
    "title": "updateBlogContent",
    "name": "updateBlogContent",
    "group": "Admin_Blog_content",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"blog_id\":1,\n\"title\":\"test\",\n\"subtitle\":\"demo\",\n\"blog_data\":\"<p></p>\"\n}\nfile:ob.jpg //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Blog content updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/BlogController.php",
    "groupTitle": "Admin_Blog_content"
  },
  {
    "type": "post",
    "url": "addAdvertiseLink",
    "title": "addAdvertiseLink",
    "name": "addAdvertiseLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"advertise_link_id\":2,\n\"sub_category_id\":31\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Linked Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addAdvertiseServerId",
    "title": "addAdvertiseServerId",
    "name": "addAdvertiseServerId",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"advertise_category_id\":1, //compulsory\n\"sub_category_id\":10, //compulsory\n\"server_id\":\"vdfjdsjhfbhjbjd\" //compulsory\n\"sub_category_advertise_server_id\":\"vdfjdsjhfbhjbjd\"\n\"device_platform\":1 //compulsory 1=Ios, 2=Android\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise server id added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addAdvertisementCategory",
    "title": "addAdvertisementCategory",
    "name": "addAdvertisementCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":1,\n\"name\":\"Banner\"\n}\nfile:image.jpeg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addAppContentViaMigration",
    "title": "addAppContentViaMigration",
    "name": "addAppContentViaMigration",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"sub_category_id\":1,\n\"is_free\":1,//optional\n\"name\":\"Nature-2017\",\n\"is_featured\":1 //compulsory\n}\nfile:image.jpeg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addCatalog",
    "title": "addCatalog",
    "name": "addCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":1,\n\"sub_category_id\":1,\n\"is_free\":1,\n\"name\":\"Nature-2017\",\n\"is_featured\":1 //0=normal 1=featured\n}\nfile:image.jpeg //compulsory",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addCatalogImages",
    "title": "addCatalogImages",
    "name": "addCatalogImages",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":1,\n\"catalog_id\":1,\n\"is_featured\":1, //1=featured catalog, 0=normal catalog\n}\nfile[]:image.jpeg\nfile[]:image12.jpeg\nfile[]:image.png",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category images added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addCatalogImagesForJson",
    "title": "addCatalogImagesForJson",
    "name": "addCatalogImagesForJson",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":0,\n\"is_replace\":0 //0=do not replace the existing file, 2=replace the existing file\n}\nfile[]:1.jpg\nfile[]:2.jpg\nfile[]:3.jpg\nfile[]:4.jpg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Resource images added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addFeaturedBackgroundCatalogImage",
    "title": "addFeaturedBackgroundCatalogImage",
    "name": "addFeaturedBackgroundCatalogImage",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":1,\n\"catalog_id\":1,\n\"image_type\":1,\n\"is_featured\":1 //1=featured catalog, 0=normal catalog\n}\noriginal_img:image1.jpeg\ndisplay_img:image12.jpeg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured background images added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addFont",
    "title": "addFont",
    "name": "addFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":4,\n\"catalog_id\":280,\n\"ios_font_name\":\"3d\", //optional\n\"is_replace\":1 //1=replace font file, 0=don't replace font file\n\"is_featured\":1 //1=featured catalog, 0=normal catalog\n}\nfile:3d.ttf",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Font added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addInvalidFont",
    "title": "addInvalidFont",
    "name": "addInvalidFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"$category_id\":1, //optional\n\"catalog_id\":280, //compulsory\n\"ios_font_name\":\"3d\", //optional\n\"is_replace\":1 //compulsory 1=replace font file, 0=don't replace font file\n\"is_featured\":1 //optional 1=featured catalog, 0=normal catalog\n}\nfile:3d.ttf //compulsory",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Font added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addJson",
    "title": "addJson",
    "name": "addJson",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:\n{\n\"category_id\": 2,\n\"catalog_id\": 646,\n\"is_featured_catalog\": 1, //1=featured catalog, 0=normal catalog\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1, //optional 1=portrait, 0=landscape\n\"search_category\": \"\", //optional\n\"json_data\": {\n\"text_json\": [\n{\n\"xPos\": 46,\n\"yPos\": 204,\n\"color\": \"#ff5d5b\",\n\"text\": \"GYM\\nNAME\",\n\"size\": 80,\n\"fontName\": \"AgencyFB-Bold\",\n\"fontPath\": \"fonts/AGENCYB.ttf\",\n\"alignment\": 1,\n\"bg_image\": \"\",\n\"texture_image\": \"\",\n\"opacity\": 100,\n\"angle\": 0,\n\"shadowColor\": \"#000000\",\n\"shadowRadius\": 0,\n\"shadowDistance\": 0\n}\n],\n\"sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"width\": 650,\n\"height\": 800,\n\"sticker_image\": \"fitness_effect_rbg3_93.png\",\n\"angle\": 0,\n\"is_round\": 0\n}\n],\n\"image_sticker_json\": [],\n\"frame_json\": {\n\"frame_image\": \"\",\n\"frame_color\": \"\"\n},\n\"background_json\": {\n\"background_image\": \"fitness_bg_rbg3_93.jpg\",\n\"background_color\": \"\"\n},\n\"sample_image\": \"fitness_sample_rbg3_93.jpg\",\n\"height\": 800,\n\"width\": 650,\n\"is_portrait\": 1,\n\"is_featured\": 0\n}\n}\nfile:image1.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addLink",
    "title": "addLink",
    "name": "addLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"sub_category_id\":46,\n\"name\":\"QR Scanner\",\n\"url\":\"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en\",\n\"platform\":\"Android\",\n\"app_description\":\"This is test description.\"\n}\nfile:ob.png\nlogo_file:logo_image.png",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Link added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addPromoCode",
    "title": "addPromoCode",
    "name": "addPromoCode",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"promo_code\":\"123\",\n\"package_name\":\"com.bg.invitationcardmaker\",\n\"device_udid\":\"e9e24a9ce6ca5498\",\n\"device_platform\":1 //1=android, 2=ios\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Promo code added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addSearchCategoryTag",
    "title": "addSearchCategoryTag",
    "name": "addSearchCategoryTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":2, //compulsory\n\"tag_name\":\"Nature\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Tag added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addTag",
    "title": "addTag",
    "name": "addTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"tag_name\":\"Nature\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Tag added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addTemplateByZip",
    "title": "addTemplateByZip",
    "name": "addTemplateByZip",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"request_data\":{\n\"catalog_id\":1,\n\"search_category\":1 //optional\n},\n\"file\":1.zip",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Template added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/ZipController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addValidation",
    "title": "addValidation",
    "name": "addValidation",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\": 2, //compulsory\n\"validation_name\": \"sticker_image_size\", //compulsory\n\"max_value_of_validation\": 100, //compulsory\n\"is_featured\":1, //compulsory\n\"is_catalog\":1, //compulsory\n\"description\":\"test\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Validation added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "changePassword",
    "title": "changePassword",
    "name": "changePassword",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"current_password\":\"**********\",\n\"new_password\":\"***********\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Password updated successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/LoginController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "clearRedisCache",
    "title": "clearRedisCache",
    "name": "clearRedisCache",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Keys Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": \"{}\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteAdvertiseServerId",
    "title": "deleteAdvertiseServerId",
    "name": "deleteAdvertiseServerId",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_advertise_server_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise server id deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteAdvertisementCategory",
    "title": "deleteAdvertisementCategory",
    "name": "deleteAdvertisementCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":1,\n\"name\":\"Nature\"\n}\nfile:image.jpeg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteAllUserFeeds",
    "title": "deleteAllUserFeeds",
    "name": "deleteAllUserFeeds",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"sub_category_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All images deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteCatalog",
    "title": "deleteCatalog",
    "name": "deleteCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":3 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog deleted successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteCatalogImage",
    "title": "deleteCatalogImage",
    "name": "deleteCatalogImage",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"img_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Normal image deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteCategory",
    "title": "deleteCategory",
    "name": "deleteCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Category deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteFont",
    "title": "deleteFont",
    "name": "deleteFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"font_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Font deleted successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteLink",
    "title": "deleteLink",
    "name": "deleteLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"advertise_link_id:1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Link Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteLinkedAdvertisement",
    "title": "deleteLinkedAdvertisement",
    "name": "deleteLinkedAdvertisement",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"advertise_link_id\":57,\n\"sub_category_id\":47\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertisement unlinked successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteLinkedCatalog",
    "title": "deleteLinkedCatalog",
    "name": "deleteLinkedCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":2,\n\"sub_category_id\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog unlinked Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteRedisKeys",
    "title": "deleteRedisKeys",
    "name": "deleteRedisKeys",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"keys_list\": [\n{\n\"key\": \"pel:getImagesByCatalogId33-1\"\n},\n{\n\"key\": \"pel:getImagesByCatalogId51-1\"\n},\n{\n\"key\":\"pel:getImagesByCatalogId57-1\"\n}\n\n]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Keys Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": \"{}\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteSearchCategoryTag",
    "title": "deleteSearchCategoryTag",
    "name": "deleteSearchCategoryTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_tag_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Search category deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteSubCategory",
    "title": "deleteSubCategory",
    "name": "deleteSubCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":3 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub category deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteTag",
    "title": "deleteTag",
    "name": "deleteTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"tag_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Tag deleted successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteUserFeeds",
    "title": "deleteUserFeeds",
    "name": "deleteUserFeeds",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"user_feeds_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Image deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteValidation",
    "title": "deleteValidation",
    "name": "deleteValidation",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"setting_id\":7 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Validation deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "disable2faByAdmin",
    "title": "disable2faByAdmin",
    "name": "disable2faByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"verify_code\": 123456,\n\"google2fa_secret\":\"ABCDEF\"\n\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"2FA has been disabled successfully\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjAuMTEzL3Bob3RvYWRraW5nX3Rlc3RpbmcvYXBpL3B1YmxpYy9hcGkvZG9Mb2dpbkZvckFkbWluIiwiaWF0IjoxNTQ3MzQ5NDY2LCJleHAiOjE1NDc5NTQyNjYsIm5iZiI6MTU0NzM0OTQ2NiwianRpIjoieDA5WUNoWUtudHlwYklWdiJ9.SifYqWURQBhpTG3jocKV1ng-zLx2KSeiCebwUKbl-E0\",\n\"user_detail\": {\n\"id\": 1,\n\"user_name\": \"admin@gmail.com\",\n\"email_id\": \"admin@gmail.com\",\n\"google2fa_enable\": 0,\n\"google2fa_secret\": \"7A7RMQ33CHLQQU5E\",\n\"social_uid\": null,\n\"signup_type\": null,\n\"profile_setup\": 1,\n\"mailchimp_subscr_id\": null,\n\"is_active\": 1,\n\"is_verify\": 1,\n\"create_time\": \"2018-09-21 06:37:46\",\n\"update_time\": \"2019-01-13 07:40:51\",\n\"attribute1\": null,\n\"attribute2\": null,\n\"attribute3\": null,\n\"attribute4\": null\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/Google2faController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "doLogin",
    "title": "doLogin",
    "name": "doLogin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"demo@gmail.com\",\n\"password\":\"123456\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\",\n\"user_detail\": {\n\"id\": 1,\n\"user_name\": \"admin\",\n\"email_id\": \"admin@gmail.com\",\n\"social_uid\": null,\n\"signup_type\": null,\n\"profile_setup\": 0,\n\"is_active\": 1,\n\"create_time\": \"2017-05-05 09:57:26\",\n\"update_time\": \"2017-07-06 13:19:13\",\n\"attribute1\": null,\n\"attribute2\": null,\n\"attribute3\": null,\n\"attribute4\": null\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/LoginController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "editAdvertisementCategory",
    "title": "editAdvertisementCategory",
    "name": "editAdvertisementCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":1,\n\"name\":\"Nature\"\n}\nfile:image.jpeg",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "editFont",
    "title": "editFont",
    "name": "editFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"font_id\":1, //compulsory\n\"ios_font_name\":\"3d\", //optional\n\"android_font_name\":\"3d.ttf\" //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Font edited successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "editJsonData",
    "title": "editJsonData",
    "name": "editJsonData",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\": 2,\n\"is_featured_catalog\": 1, //1=featured catalog, 0=normal catalog\n\"img_id\": 356,\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1, //optional 1=portrait, 0=landscape\n\"json_data\": {//optional\n\"text_json\": [\n{\n\"xPos\": 46,\n\"yPos\": 204,\n\"color\": \"#ff5d5b\",\n\"text\": \"GYM\\nNAME\",\n\"size\": 80,\n\"fontName\": \"AgencyFB-Bold\",\n\"fontPath\": \"fonts/AGENCYB.ttf\",\n\"alignment\": 1,\n\"bg_image\": \"\",\n\"texture_image\": \"\",\n\"opacity\": 100,\n\"angle\": 0,\n\"shadowColor\": \"#000000\",\n\"shadowRadius\": 0,\n\"shadowDistance\": 0\n}\n],\n\"sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"width\": 650,\n\"height\": 800,\n\"sticker_image\": \"fitness_effect_rbg3_93.png\",\n\"angle\": 0,\n\"is_round\": 0\n}\n],\n\"image_sticker_json\": [],\n\"frame_json\": {\n\"frame_image\": \"\",\n\"frame_color\": \"\"\n},\n\"background_json\": {\n\"background_image\": \"fitness_bg_rbg3_93.jpg\",\n\"background_color\": \"\"\n},\n\"sample_image\": \"fitness_sample_rbg3_93.jpg\",\n\"height\": 800,\n\"width\": 650,\n\"is_portrait\": 1,\n\"is_featured\": 0\n}\n}\nfile:image1.jpeg //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json data updated successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "editValidation",
    "title": "editValidation",
    "name": "editValidation",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"setting_id\":1, //compulsory\n\"category_id\":0, //compulsory\n\"validation_name\":\"common_image_size\", //compulsory\n\"max_value_of_validation\":200, //compulsory\n\"is_featured\":0, //compulsory\n\"is_catalog\":0, //compulsory\n\"description\":\"Maximum size for all common images.\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Validation updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "enable2faByAdmin",
    "title": "enable2faByAdmin",
    "name": "enable2faByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"2FA has been enabled successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"google2fa_url\": \"https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth%3A%2F%2Ftotp%2FOB%2520ADS%3Aadmin%2540gmail.com%3Fsecret%3D3WJMFHPL2XBLWNT3%26issuer%3DOB%2520ADS\",\n\"google2fa_secret\": \"JMFHPL2XBLWNT3\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/Google2faController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAdvertiseLink",
    "title": "getAdvertiseLink",
    "name": "getAdvertiseLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":31,\n\"platform\":\"ios\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"link_list\": [\n{\n\"advertise_link_id\": 44,\n\"name\": \"Suitescene\",\n\"platform\": \"iOS\",\n\"linked\": 1\n},\n{\n\"advertise_link_id\": 41,\n\"name\": \"Bhavesh Gabani\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 40,\n\"name\": \"Visa\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 39,\n\"name\": \"QR Code Scanner : Barcode QR-Code Generator App\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 38,\n\"name\": \"PhotoEditorLab � Stickers , Filters & Frames\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 37,\n\"name\": \"QR Barcode Scanner : QR Bar Code Generator App\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 36,\n\"name\": \"Cut Paste - Background Eraser\",\n\"platform\": \"iOS\",\n\"linked\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAdvertiseServerIdForAdmin",
    "title": "getAdvertiseServerIdForAdmin",
    "name": "getAdvertiseServerIdForAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":66 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise server id fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"advertise_category_id\": 3,\n\"advertise_category\": \"Rewarded Video\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:07:07\",\n\"update_time\": \"2018-07-16 09:07:07\",\n\"android\": [],\n\"ios\": [\n{\n\"sub_category_advertise_server_id\": 1,\n\"advertise_category_id\": 3,\n\"sub_category_id\": 66,\n\"server_id\": \"Test Rewarded Video Ad Id 1\",\n\"device_platform\": 1,\n\"is_active\": 1,\n\"create_time\": \"2018-07-18 09:09:22\",\n\"update_time\": \"2018-07-18 09:09:22\"\n}\n]\n},\n{\n\"advertise_category_id\": 1,\n\"advertise_category\": \"Banner\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\",\n\"android\": [\n{\n\"sub_category_advertise_server_id\": 2,\n\"advertise_category_id\": 1,\n\"sub_category_id\": 66,\n\"server_id\": \"Test Banner Ad Id 1\",\n\"device_platform\": 2,\n\"is_active\": 1,\n\"create_time\": \"2018-07-18 09:10:23\",\n\"update_time\": \"2018-07-18 09:10:23\"\n}\n],\n\"ios\": []\n},\n{\n\"advertise_category_id\": 2,\n\"advertise_category\": \"Intertial\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\",\n\"android\": [],\n\"ios\": []\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllAdvertiseCategory",
    "title": "getAllAdvertiseCategory",
    "name": "getAllAdvertiseCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise categories fetched successfully.\",\n\"cause\": \"\",\n\"data\": [\n{\n\"advertise_category_id\": 3,\n\"advertise_category\": \"Rewarded Video\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:07:07\",\n\"update_time\": \"2018-07-16 09:07:07\"\n},\n{\n\"advertise_category_id\": 1,\n\"advertise_category\": \"Banner\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\"\n},\n{\n\"advertise_category_id\": 2,\n\"advertise_category\": \"Intertial\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\"\n}\n]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllAdvertisementToLinkAdvertisement",
    "title": "getAllAdvertisementToLinkAdvertisement",
    "name": "getAllAdvertisementToLinkAdvertisement",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":63\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertisements fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"advertise_link_id\": 79,\n\"name\": \"Invitation Maker Card Creator\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"url\": \"https://itunes.apple.com/mu/app/invitation-maker-card-creator/id1320828574?mt=8\",\n\"platform\": \"iOS\",\n\"app_description\": \"Create your own invitation card for party, birthday, wedding ceremony, engagement/ring ceremony within seconds using beautiful and professional templates.\",\n\"linked\": 1\n},\n{\n\"advertise_link_id\": 78,\n\"name\": \"Digital Business Card Maker\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a158980cd_banner_image_1515561304.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a158980cd_banner_image_1515561304.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a158980cd_banner_image_1515561304.jpg\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a15977977_app_logo_image_1515561305.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a15977977_app_logo_image_1515561305.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a15977977_app_logo_image_1515561305.png\",\n\"url\": \"https://itunes.apple.com/mu/app/digital-business-card-maker/id1316860834?mt=8\",\n\"platform\": \"iOS\",\n\"app_description\": \"Create your own business card within seconds using beautiful and professional templates.\",\n\"linked\": 1\n},\n{\n\"advertise_link_id\": 77,\n\"name\": \"Romantic Love Photo Editor\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor\",\n\"platform\": \"Android\",\n\"app_description\": \"Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc.\",\n\"linked\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllAdvertisements",
    "title": "getAllAdvertisements",
    "name": "getAllAdvertisements",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1,\n\"item_count\":2\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 42,\n\"is_next_page\": true,\n\"result\": [\n{\n\"advertise_link_id\": 79,\n\"name\": \"Invitation Maker Card Creator\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e834d53_banner_image_1515561448.jpg\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a1e88910f_app_logo_image_1515561448.png\",\n\"url\": \"https://itunes.apple.com/mu/app/invitation-maker-card-creator/id1320828574?mt=8\",\n\"platform\": \"iOS\",\n\"app_description\": \"Create your own invitation card for party, birthday, wedding ceremony, engagement/ring ceremony within seconds using beautiful and professional templates.\"\n},\n{\n\"advertise_link_id\": 78,\n\"name\": \"Digital Business Card Maker\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a158980cd_banner_image_1515561304.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a158980cd_banner_image_1515561304.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a158980cd_banner_image_1515561304.jpg\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a55a15977977_app_logo_image_1515561305.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a55a15977977_app_logo_image_1515561305.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a55a15977977_app_logo_image_1515561305.png\",\n\"url\": \"https://itunes.apple.com/mu/app/digital-business-card-maker/id1316860834?mt=8\",\n\"platform\": \"iOS\",\n\"app_description\": \"Create your own business card within seconds using beautiful and professional templates.\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllCatalog",
    "title": "getAllCatalog",
    "name": "getAllCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"catalog_list\": [\n{\n\"catalog_id\": 17,\n\"sub_category_id\": 4,\n\"name\": \"Dragon Tattoo\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59895317393b0_catalog_img_1502171927.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59895317393b0_catalog_img_1502171927.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59895317393b0_catalog_img_1502171927.png\",\n\"is_free\": 1\n},\n{\n\"catalog_id\": 17,\n\"sub_category_id\": 10,\n\"name\": \"Dragon Tattoo\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59895317393b0_catalog_img_1502171927.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59895317393b0_catalog_img_1502171927.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59895317393b0_catalog_img_1502171927.png\",\n\"is_free\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllCategory",
    "title": "getAllCategory",
    "name": "getAllCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"page\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All category fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 8,\n\"is_next_page\": false,\n\"category_list\": [\n{\n\"category_id\": 9,\n\"name\": \"demo 3\"\n},\n{\n\"category_id\": 8,\n\"name\": \"demo 2\"\n},\n{\n\"category_id\": 7,\n\"name\": \"demo1\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllFonts",
    "title": "getAllFonts",
    "name": "getAllFonts",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"catalog_id\":1, //compulsory\n \"order_by\":1, //optional\n \"order_type\":1 //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Fonts fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_count\": 10,\n\"result\": [\n{\n\"font_id\": 94,\n\"font_name\": \"Baloo Thambi Regular\",\n\"font_file\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_thambi_regular.ttf\",\n\"ios_font_name\": \"Baloo Thambi Regular\",\n\"android_font_name\": \"fonts/baloo_thambi_regular.ttf\",\n\"is_active\": 1\n},\n{\n\"font_id\": 93,\n\"font_name\": \"Baloo Tammudu Regular\",\n\"font_file\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_tammudu_regular.ttf\",\n\"ios_font_name\": \"Baloo Tammudu Regular\",\n\"android_font_name\": \"fonts/baloo_tammudu_regular.ttf\",\n\"is_active\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllFontsByCatalogIdForAdmin",
    "title": "getAllFontsByCatalogIdForAdmin",
    "name": "getAllFontsByCatalogIdForAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"catalog_id\":1, //compulsory\n \"order_by\":1, //optional\n \"order_type\":1 //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Fonts fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_count\": 10,\n\"result\": [\n{\n\"font_id\": 94,\n\"font_name\": \"Baloo Thambi Regular\",\n\"font_file\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_thambi_regular.ttf\",\n\"ios_font_name\": \"Baloo Thambi Regular\",\n\"android_font_name\": \"fonts/baloo_thambi_regular.ttf\",\n\"is_active\": 1\n},\n{\n\"font_id\": 93,\n\"font_name\": \"Baloo Tammudu Regular\",\n\"font_file\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/baloo_tammudu_regular.ttf\",\n\"ios_font_name\": \"Baloo Tammudu Regular\",\n\"android_font_name\": \"fonts/baloo_tammudu_regular.ttf\",\n\"is_active\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllLink",
    "title": "getAllLink",
    "name": "getAllLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"page\":1,\n\"item_count\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"is_next_page\": false,\n\"link_list\": [\n{\n\"advertise_link_id\": 51,\n\"name\": \"QR Scanner\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437f82a94f_banner_image_1510225912.jpg\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437f82a94f_banner_image_1510225912.jpg\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a0437f82a94f_banner_image_1510225912.jpg\",\n\"app_logo_thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437f82ad37_app_logo_image_1510225912.jpg\",\n\"app_logo_compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437f82ad37_app_logo_image_1510225912.jpg\",\n\"app_logo_original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a0437f82ad37_app_logo_image_1510225912.jpg\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en\",\n\"platform\": \"Android\",\n\"app_description\": \"This is test description\"\n},\n{\n\"advertise_link_id\": 52,\n\"name\": \"QR Scanner\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a04375d4c4ed_banner_image_1510225757.jpg\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a04375d4c4ed_banner_image_1510225757.jpg\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a04375d4c4ed_banner_image_1510225757.jpg\",\n\"app_logo_thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a0437600e172_app_logo_image_1510225760.jpeg\",\n\"app_logo_compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a0437600e172_app_logo_image_1510225760.jpeg\",\n\"app_logo_original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a0437600e172_app_logo_image_1510225760.jpeg\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en\",\n\"platform\": \"Android\",\n\"app_description\": \"This is test description.\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllPromoCode",
    "title": "getAllPromoCode",
    "name": "getAllPromoCode",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_type\":\"asc\",\n\"order_by\":\"promo_code\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Promo codes fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"promo_code_id\": 1,\n\"promo_code\": \"123\",\n\"package_name\": \"com.bg.invitationcardmaker\",\n\"device_udid\": \"e9e24a9ce6ca5498\",\n\"device_platform\": 1,\n\"status\": 0,\n\"create_time\": \"2018-05-15 09:50:49\",\n\"update_time\": \"2018-05-15 09:50:49\"\n},\n{\n\"promo_code_id\": 2,\n\"promo_code\": \"test 1\",\n\"package_name\": \"test 2\",\n\"device_udid\": \"test 3\",\n\"device_platform\": 1,\n\"status\": 0,\n\"create_time\": \"2018-05-15 10:02:35\",\n\"update_time\": \"2018-05-15 10:02:35\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllRestoreDevice",
    "title": "getAllRestoreDevice",
    "name": "getAllRestoreDevice",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"page\":1,\n\"item_count\":30,\n\"order_by\":\"order_number\",\n\"order_type\":\"ASC\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"App Restore Device Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 9,\n\"is_next_page\": false,\n\"list_device\": [\n{\n\"id\": 1,\n\"order_number\": \"Null1494507231573\",\n\"device_udid\": \"ff505a7a500c931a\",\n\"restore\": 1,\n\"create_time\": \"2017-05-11 12:53:49\",\n\"update_time\": \"2017-05-11 18:23:49\"\n},\n{\n\"id\": 2,\n\"order_number\": \"Null1494508061564\",\n\"device_udid\": \"ff505a7a500c931a\",\n\"restore\": 1,\n\"create_time\": \"2017-05-11 13:07:39\",\n\"update_time\": \"2017-05-11 18:37:39\"\n},\n{\n\"id\": 3,\n\"order_number\": \"Null1494508092829\",\n\"device_udid\": \"ff505a7a500c931a\",\n\"restore\": 1,\n\"create_time\": \"2017-05-11 13:08:11\",\n\"update_time\": \"2017-05-11 18:38:11\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllSubCategory",
    "title": "getAllSubCategory",
    "name": "getAllSubCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub categories fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 13,\n\"category_list\": [\n{\n\"sub_category_id\": 86,\n\"category_id\": 1,\n\"sub_category_name\": \"Background Changer Frame\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5b333358a7cf6_category_img_1530082136.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5b333358a7cf6_category_img_1530082136.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5b333358a7cf6_category_img_1530082136.png\",\n\"is_featured\": 0\n},\n{\n\"sub_category_id\": 79,\n\"category_id\": 1,\n\"sub_category_name\": \"Video Flyer Frame\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5afe5d71c4f60_category_img_1526619505.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5afe5d71c4f60_category_img_1526619505.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5afe5d71c4f60_category_img_1526619505.jpg\",\n\"is_featured\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllSubCategoryForLinkCatalog",
    "title": "getAllSubCategoryForLinkCatalog",
    "name": "getAllSubCategoryForLinkCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":18,\n\"sub_category_id\":20\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"SubCategory Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"sub_category_id\": 13,\n\"name\": \"Background\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 12,\n\"name\": \"Frames\",\n\"linked\": 1\n},\n{\n\"sub_category_id\": 10,\n\"name\": \"Goggles\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 9,\n\"name\": \"Hair Style\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 4,\n\"name\": \"Tattoos\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 11,\n\"name\": \"Turbans\",\n\"linked\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllSubCategoryToMoveTemplate",
    "title": "getAllSubCategoryToMoveTemplate",
    "name": "getAllSubCategoryToMoveTemplate",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"img_id\":3386, //compulsory\n\"category_id\":2, //optional (If this arg is not pass then it will return sub_categories from all categories)\n\"is_featured\":1 //1=featured catalog, 0=normal catalog\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub categories are fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"sub_category_list\": [\n{\n\"sub_category_id\": 66,\n\"sub_category_name\": \"All Templates\",\n\"catalog_list\": [\n{\n\"catalog_id\": 508,\n\"catalog_name\": \"Dhruvit\",\n\"is_linked\": 0\n}\n]\n},\n{\n\"sub_category_id\": 88,\n\"sub_category_name\": \"Baby Photo Maker\",\n\"catalog_list\": [\n{\n\"catalog_id\": 274,\n\"catalog_name\": \"Baby Collage\",\n\"is_linked\": 0\n},\n{\n\"catalog_id\": 249,\n\"catalog_name\": \"Baby with Parents\",\n\"is_linked\": 0\n}\n]\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllTags",
    "title": "getAllTags",
    "name": "getAllTags",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All tags fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 4,\n\"result\": [\n{\n\"tag_id\": 1,\n\"tag_name\": \"test\"\n},\n{\n\"tag_id\": 2,\n\"tag_name\": \"Offer & Sales\"\n},\n{\n\"tag_id\": 3,\n\"tag_name\": \"Mobile Apps\"\n},\n{\n\"tag_id\": 4,\n\"tag_name\": \"Photography\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllUser",
    "title": "getAllUser",
    "name": "getAllUser",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"page\":1,\n\"item_count\":30,\n\"order_by\":\"device_id\",//optinal\n\"order_type\":\"ASC\"//optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All User Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 15,\n\"list_user\": [\n{\n\"device_id\": 15,\n\"device_reg_id\": \"AD64EF08F134750CC09D489F90B9DFF623A91FC84AD3A9468A068E679B5C83D7\",\n\"device_platform\": \"ios\",\n\"device_model_name\": \"iPhone\",\n\"device_vendor_name\": \"Apple\",\n\"device_os_version\": \"10.0.2\",\n\"device_udid\": \"A70B3F58-75EF-4C76-ADEC-14E93D57887E\",\n\"device_resolution\": \"320.0568.0\",\n\"device_carrier\": \"\",\n\"device_country_code\": \"US\",\n\"device_language\": \"en-US\",\n\"device_local_code\": \"en-US\",\n\"device_default_time_zone\": \"Pacific/Chatham\",\n\"device_library_version\": \"\",\n\"device_application_version\": \"1.0.3\",\n\"device_type\": \"phone\",\n\"device_registration_date\": \"2017-05-12 05:07:33 +0000\",\n\"is_active\": 1,\n\"is_count\": 5,\n\"create_time\": \"2017-06-03 10:38:03\",\n\"update_time\": \"2017-06-24 13:17:14\"\n},\n{\n\"device_id\": 14,\n\"device_reg_id\": \"\",\n\"device_platform\": \"ios\",\n\"device_model_name\": \"iPhone\",\n\"device_vendor_name\": \"Apple\",\n\"device_os_version\": \"10.3.1\",\n\"device_udid\": \"C104766F-B9CE-49D8-BEB3-101F04E849CF\",\n\"device_resolution\": \"375.0667.0\",\n\"device_carrier\": \"\",\n\"device_country_code\": \"US\",\n\"device_language\": \"en-US\",\n\"device_local_code\": \"en-US\",\n\"device_default_time_zone\": \"Asia/Kolkata\",\n\"device_library_version\": \"\",\n\"device_application_version\": \"1.0.3\",\n\"device_type\": \"phone\",\n\"device_registration_date\": \"2017-05-24 05:43:18 +0000\",\n\"is_active\": 1,\n\"is_count\": 5,\n\"create_time\": \"2017-05-24 11:13:18\",\n\"update_time\": \"2017-06-24 13:17:14\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllValidationsForAdmin",
    "title": "getAllValidationsForAdmin",
    "name": "getAllValidationsForAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All validations fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"setting_id\": 1,\n\"category_id\": 0,\n\"validation_name\": \"common_image_size\",\n\"max_value_of_validation\": \"100\",\n\"is_featured\": 0,\n\"is_catalog\": 0,\n\"description\": \"Maximum size for all common images. asasda dasd asd\",\n\"update_time\": \"2019-07-17 06:08:01\"\n}\n],\n\"category_list\": [\n{\n\"category_id\": 1,\n\"name\": \"Frame\"\n},\n{\n\"category_id\": 2,\n\"name\": \"Sticker\"\n},\n{\n\"category_id\": 3,\n\"name\": \"Background\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getCategoryTagBySubCategoryId",
    "title": "getCategoryTagBySubCategoryId",
    "name": "getCategoryTagBySubCategoryId",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1, //compulsory\n\"order_by\":\"tag_name\", //optional\n\"order_type\":\"ASC\" //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Category tags fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 10,\n\"result\": [\n{\n\"sub_category_tag_id\": 18,\n\"tag_name\": \"dats\",\n\"total_template\": 0\n},\n{\n\"sub_category_tag_id\": 12,\n\"tag_name\": \"Birthday\",\n\"total_template\": 24\n},\n{\n\"sub_category_tag_id\": 16,\n\"tag_name\": \"Flag\",\n\"total_template\": 20\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getDataByCatalogIdForAdmin",
    "title": "getDataByCatalogIdForAdmin",
    "name": "getDataByCatalogIdForAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"catalog_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Data fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 182,\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d5644e1424_catalog_image_1502434884.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d5644e1424_catalog_image_1502434884.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d5644e1424_catalog_image_1502434884.png\",\n\"is_json_data\": 0,\n\"json_data\": \"\",\n\"is_featured\": \"\",\n\"is_free\": 0,\n\"is_portrait\": 0,\n\"search_category\": \"\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getImageDetails",
    "title": "getImageDetails",
    "name": "getImageDetails",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1,\n\"item_count\":10,\n\"order_by\":\"size\",\n\"order_type\":\"ASC\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All User Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 36,\n\"is_next_page\": true,\n\"image_details\": [\n{\n\"name\": \"59687a44dcae2_background_img_1500019268.png\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687a44dcae2_background_img_1500019268.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687a44dcae2_background_img_1500019268.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687a44dcae2_background_img_1500019268.png\",\n\"directory_name\": \"compress\",\n\"type\": \"png\",\n\"size\": 4572880,\n\"height\": 1440,\n\"width\": 1920,\n\"created_at\": \"2017-07-14 08:01:11\"\n},\n{\n\"name\": \"59687aeb86626_background_img_1500019435.png\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687aeb86626_background_img_1500019435.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687aeb86626_background_img_1500019435.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687aeb86626_background_img_1500019435.png\",\n\"directory_name\": \"compress\",\n\"type\": \"png\",\n\"size\": 3820520,\n\"height\": 1904,\n\"width\": 2000,\n\"created_at\": \"2017-07-14 08:03:57\"\n},\n{\n\"name\": \"59687aa95d6be_background_img_1500019369.png\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59687aa95d6be_background_img_1500019369.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59687aa95d6be_background_img_1500019369.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59687aa95d6be_background_img_1500019369.png\",\n\"directory_name\": \"original\",\n\"type\": \"png\",\n\"size\": 2863220,\n\"height\": 2000,\n\"width\": 2000,\n\"created_at\": \"2017-07-14 08:02:50\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getPurchaseUser",
    "title": "getPurchaseUser",
    "name": "getPurchaseUser",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"page\":1,\n\"item_count\":30,\n\"order_by\":\"order_number\",//optional\n\"order_type\":\"ASC\"//optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Purchase User Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 9,\n\"list_user\": [\n{\n\"order_number\": \"Null1494507231573\",\n\"tot_order_amount\": 2.99,\n\"currency_code\": \"USD\",\n\"device_platform\": \"android\",\n\"create_time\": \"2017-05-11 18:23:49\"\n},\n{\n\"order_number\": \"Null1494508061564\",\n\"tot_order_amount\": 2.99,\n\"currency_code\": \"USD\",\n\"device_platform\": \"android\",\n\"create_time\": \"2017-05-11 18:37:39\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getRedisKeyDetail",
    "title": "getRedisKeyDetail",
    "name": "getRedisKeyDetail",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"key\": \"pel:getSubCategoryByCategoryId9-1\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Key Detail Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"keys_detail\": [\n{\n\"category_id\": 11,\n\"name\": \"Testing\"\n},\n{\n\"category_id\": 10,\n\"name\": \"Frame\"\n},\n{\n\"category_id\": 9,\n\"name\": \"Sticker\"\n},\n{\n\"category_id\": 1,\n\"name\": \"Background\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getRedisKeys",
    "title": "getRedisKeys",
    "name": "getRedisKeys",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Keys Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"keys_list\": [\n\"pel:I4IGClzRXZAjA9u8\",\n\"pel:getCatalogBySubCategoryId56-1\",\n\"pel:getAllCategory1\",\n\"pel:AV4SJwr8Rrf8O60a\",\n\"pel:getBackgroundCategory1\",\n\"pel:598068d3311b6315293306:standard_ref\",\n\"pel:tag:role_user:key\",\n\"pel:getLinkiOS-1\",\n\"pel:Btr0iNfysqBDree8\",\n\"pel:hNBS6Vxc66wL3Dux\"\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getSampleImagesForAdmin",
    "title": "getSampleImagesForAdmin",
    "name": "getSampleImagesForAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":13\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Images Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 220,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33bf5cd88_original_img_1502360511.png\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33bf5cd88_original_img_1502360511.png\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33bf5cd88_original_img_1502360511.png\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33c010ed8_display_img_1502360512.png\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33c010ed8_display_img_1502360512.png\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33c010ed8_display_img_1502360512.png\",\n\"image_type\": 1\n},\n{\n\"img_id\": 219,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c3141d844a_original_img_1502359873.png\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c3141d844a_original_img_1502359873.png\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c3141d844a_original_img_1502359873.png\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c314294e53_display_img_1502359874.png\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c314294e53_display_img_1502359874.png\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c314294e53_display_img_1502359874.png\",\n\"image_type\": 1\n},\n{\n\"img_id\": 216,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e07757_original_img_1502345806.jpg\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e07757_original_img_1502345806.jpg\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e07757_original_img_1502345806.jpg\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e39443_display_img_1502345806.jpg\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e39443_display_img_1502345806.jpg\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e39443_display_img_1502345806.jpg\",\n\"image_type\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getSamplesOfNonCommercialFont",
    "title": "getSamplesOfNonCommercialFont",
    "name": "getSamplesOfNonCommercialFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"catalog_id\":1, //compulsory\n \"order_by\":1,\n \"order_type\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Images Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 360,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"is_json_data\": 0,\n\"json_data\": \"\",\n\"is_featured\": \"\",\n\"is_free\": 0\n},\n{\n\"img_id\": 359,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1697482f0a2_json_image_1511429960.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1697482f0a2_json_image_1511429960.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1697482f0a2_json_image_1511429960.jpg\",\n\"is_json_data\": 1,\n\"json_data\": \"test\",\n\"is_featured\": \"0\",\n\"is_free\": 0\n},\n{\n\"img_id\": 352,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"is_json_data\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 440,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 210,\n\"width\": 210\n},\n{\n\"xPos\": 0,\n\"yPos\": 211,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 270,\n\"width\": 430\n},\n{\n\"xPos\": 353,\n\"yPos\": 439,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 320,\n\"width\": 297\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_1.6.png\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_1.6.jpg\",\n\"height\": 800,\n\"width\": 650,\n\"is_featured\": 0\n},\n\"is_featured\": \"0\",\n\"is_free\": 1\n},\n{\n\"img_id\": 355,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"is_json_data\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7.png\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7.jpg\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n},\n\"is_featured\": \"1\",\n\"is_free\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getSearchTagsForAllNormalImages",
    "title": "getSearchTagsForAllNormalImages",
    "name": "getSearchTagsForAllNormalImages",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"img_id\": 356,\n\"is_free\": 1,\n\"is_featured\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n}\n},\nfile:image1.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json data updated successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getSearchTagsForAllSampleImages",
    "title": "getSearchTagsForAllSampleImages",
    "name": "getSearchTagsForAllSampleImages",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"img_id\": 356,\n\"is_free\": 1,\n\"is_featured\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n}\n},\nfile:image1.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json data updated successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getUserFeedsBySubCategoryId",
    "title": "getUserFeedsBySubCategoryId",
    "name": "getUserFeedsBySubCategoryId",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":45, //compulsory\n\"page\":1, //compulsory\n\"item_count\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Images fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 1,\n\"is_next_page\": false,\n\"result\": [\n{\n\"user_feeds_id\": 1,\n\"sub_category_id\": 45,\n\"json_id\": 2146,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5ae99587dc771_user_feeds_1525257607.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5ae99587dc771_user_feeds_1525257607.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5ae99587dc771_user_feeds_1525257607.jpg\",\n\"is_active\": 0,\n\"create_time\": \"2018-05-02 10:40:10\",\n\"update_time\": \"2018-05-02 10:40:10\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getUserProfile",
    "title": "getUserProfile",
    "name": "getUserProfile",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"getUserProfile Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_details\": [\n{device_platform\n\"id\": 1,\n\"first_name\": \"admin\",\n\"last_name\": \"admin\",\n\"phone_number_1\": \"9173527938\",\n\"profile_img\": \"http://localhost/bgchanger/image_bucket/thumbnail/595b4076a8c8c_profile_img_1499152502.jpg\",\n\"about_me\": \"i'm Admin.\",\n\"address_line_1\": \"Rander Road\",\n\"city\": \"surat\",\n\"state\": \"gujarat\",\n\"zip_code\": \"395010\",\n\"contry\": \"India\",\n\"latitude\": \"\",\n\"longitude\": \"\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/LoginController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "linkAdvertisementWithSubCategory",
    "title": "linkAdvertisementWithSubCategory",
    "name": "linkAdvertisementWithSubCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"advertise_link_id\":57,\n\"sub_category_id\":47\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertisement Linked Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "linkCatalog",
    "title": "linkCatalog",
    "name": "linkCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":2,\n\"sub_category_id\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Linked Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "moveTemplate",
    "title": "moveTemplate",
    "name": "moveTemplate",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":201, //compulsory\n\"template_list\":[3386] //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Template moved successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "removeInvalidFont",
    "title": "removeInvalidFont",
    "name": "removeInvalidFont",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":5, //compulsory\n\"font_ids\":\"280,281\", //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Font removed successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchCatalogByName",
    "title": "searchCatalogByName",
    "name": "searchCatalogByName",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"name\":\"black\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Search Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"catalog_id\": 26,\n\"name\": \"Black\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597ec7b66b2cb_catalog_img_1501480886.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597ec7b66b2cb_catalog_img_1501480886.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/597ec7b66b2cb_catalog_img_1501480886.png\",\nis_free = 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchCategoryByName",
    "title": "searchCategoryByName",
    "name": "searchCategoryByName",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"name\":\"fea\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Search category fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"category_id\": 1,\n\"name\": \"Featured\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchPromoCode",
    "title": "searchPromoCode",
    "name": "searchPromoCode",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"search_type\":\"promo_code\", //compulsory\n\"search_query\":\"12\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Promo code fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"promo_code_id\": 1,\n\"promo_code\": \"123\",\n\"package_name\": \"com.bg.invitationcardmaker\",\n\"device_udid\": \"e9e24a9ce6ca5498\",\n\"device_platform\": 1,\n\"status\": 0,\n\"create_time\": \"2018-05-15 09:50:49\",\n\"update_time\": \"2018-05-15 09:50:49\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchPurchaseUser",
    "title": "searchPurchaseUser",
    "name": "searchPurchaseUser",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"sub_category_id\":20,\n \"search_type\":\"order_number\",\n \"search_query\":\"10\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Purchase User Successfully fetch.\",\n\"cause\": \"\",\n\"data\": {\n\"list_user\": [\n{\n\"sub_category_id\":20,\n\"order_number\": \"1000000297266758\",\n\"tot_order_amount\": 1.99,\n\"currency_code\": \"USD\",\n\"device_platform\": \"ios\",\n\"create_time\": null\n},\n{\n\"sub_category_id\":20,\n\"order_number\": \"1100000297266758\",\n\"tot_order_amount\": 1.99,\n\"currency_code\": \"USD\",\n\"device_platform\": \"ios\",\n\"create_time\": null\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchRestoreDevice",
    "title": "searchRestoreDevice",
    "name": "searchRestoreDevice",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"sub_category_id\":20,\n \"search_type\":\"order_number\", // or device_udid\n \"search_query\":\"249\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"App Restore Device Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"list_device\": [\n{\n\"id\": 3,\n\"sub_category_id\":20,\n\"order_number\": \"2494322134113\",\n\"device_udid\": \"ff505a7a500c931a\",\n\"restore\": 1,\n\"create_time\": \"2017-07-07 10:19:58\",\n\"update_time\": \"2017-07-07 15:49:58\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchSubCategoryByName",
    "title": "searchSubCategoryByName",
    "name": "searchSubCategoryByName",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\":1, //compulsory\n\"name\":\"ca\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub category fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"sub_category_id\": 28,\n\"name\": \"Sub-category\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597c6e5045aa8_category_img_1501326928.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597c6e5045aa8_category_img_1501326928.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/597c6e5045aa8_category_img_1501326928.png\",\n\"is_featured\":1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "searchUser",
    "title": "searchUser",
    "name": "searchUser",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"sub_category_id\":20,\n \"search_type\":\"device_id\",\n \"search_query\":\"10\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Search User Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"list_user\": [\n{\n\"sub_category_id\": 20,\n\"device_id\": 10,\n\"device_reg_id\": \"911F5C2F54F94DC6037C1A249B0BC98BFF5B17DF2A1C8CE5A546201A5B8DCF6B\",\n\"device_platform\": \"android\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_vendor_name\": \"Micromax\",\n\"device_os_version\": \"6.0.1\",\n\"device_udid\": \"809111aa1121\",\n\"device_resolution\": \"480x782\",\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_language\": \"en\",\n\"device_local_code\": \"NA\",\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_library_version\": \"1\",\n\"device_application_version\": \"\",\n\"device_type\": \"phone\",\n\"device_registration_date\": \"2017-07-06T15:58:11 +0530\",\n\"is_active\": 1,\n\"is_count\": 0,\n\"create_time\": \"2017-07-07 15:48:05\",\n\"update_time\": \"2017-07-07 15:48:05\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "sendPushNotification",
    "title": "send push notification",
    "name": "sendPushNotification",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"data\": {\n\"GCM_DATA\": {\n\"sub_category_id\":1,\n\"title\": \"title1\",\n\"message\": \"message1\"\n}\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Notification sent successfully.\",\n\"cause\": \"\",\n\"response\": {\n\"notification_id\":1\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/NotificationController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "setCatalogRankOnTheTopByAdmin",
    "title": "setCatalogRankOnTheTopByAdmin",
    "name": "setCatalogRankOnTheTopByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Rank set successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "setCategoryTagRankOnTheTopByAdmin",
    "title": "setCategoryTagRankOnTheTopByAdmin",
    "name": "setCategoryTagRankOnTheTopByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_tag_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Rank set successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "setContentRankOnTheTopByAdmin",
    "title": "setContentRankOnTheTopByAdmin",
    "name": "setContentRankOnTheTopByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"img_id\":1963 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Rank set successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "unlinkAdvertise",
    "title": "unlinkAdvertise",
    "name": "unlinkAdvertise",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"advertise_link_id\":2,\n\"sub_category_id\":31\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise unLinked Successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateAdvertiseServerId",
    "title": "updateAdvertiseServerId",
    "name": "updateAdvertiseServerId",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_advertise_server_id\":1, //compulsory\n\"advertise_category_id\":1, //compulsory\n\"server_id\":\"absdjdfgjfj\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise server id updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateAllSampleImages",
    "title": "updateAllSampleImages",
    "name": "updateAllSampleImages",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"img_id\": 356,\n\"is_free\": 1,\n\"is_featured\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n}\n},\nfile:image1.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json data updated successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateCatalog",
    "title": "updateCatalog",
    "name": "updateCatalog",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":1,\n\"sub_category_id\":66,\n\"catalog_id\":1,\n\"name\":\"bg-catalog\",\n\"is_free\":1,\n\"is_featured\":1 //0=normal 1=featured\n}\nfile:image.png //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateCategory",
    "title": "updateCategory",
    "name": "updateCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\":1,\n\"name\":\"Featured\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Category updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateFeaturedBackgroundCatalogImage",
    "title": "updateFeaturedBackgroundCatalogImage",
    "name": "updateFeaturedBackgroundCatalogImage",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{//all parameters are compulsory\n\"category_id\":1,\n\"img_id\":1,\n\"image_type\":1,\n\"is_featured\":1 //1=featured catalog, 0=normal catalog\n},\noriginal_img:image1.jpeg //optional\ndisplay_img:image12.jpeg //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured background images updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateLink",
    "title": "updateLink",
    "name": "updateLink",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"sub_category_id\": 46,\n\"advertise_link_id\": 51,\n\"name\": \"QR Scanner\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en\",\n\"platform\": \"Android\",\n\"app_description\": \"This is test description\"\n}\nfile:ob.png //optional\nlogo_file:ob.png //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Link updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateSearchCategoryTag",
    "title": "updateSearchCategoryTag",
    "name": "updateSearchCategoryTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":2, //compulsory\n\"sub_category_tag_id\":1, //compulsory\n\"tag_name\":\"Featured\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Search category updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateSubCategory",
    "title": "updateSubCategory",
    "name": "updateSubCategory",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"sub_category_id\":2, //compulsory\n\"name\":\"Love-Category\", //optional\n\"is_featured\":1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)\n}\nfile:image.png //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub category updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateCatalogImage",
    "title": "updateCatalogImage",
    "name": "updateSubCategoryImage",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"category_id\":1,\n\"img_id\":1,\n\"is_featured\":1, //1=featured catalog, 0=normal catalog\n\"search_category\":\"test,abc\" //optional\n}\nfile:1.jpg //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Normal image updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateTag",
    "title": "updateTag",
    "name": "updateTag",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"tag_id\":1, //compulsory\n\"tag_name\":\"Featured\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Tag updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateUserProfile",
    "title": "updateUserProfile",
    "name": "updateUserProfile",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"first_name\":\"jitu\",\n\"last_name\":\"admin\",\n\"phone_number_1\":\"9173527938\",\n\"address_line_1\":\"Rander\",\n\"city\":\"surat\",\n\"state\":\"Gujarat\",\n\"pincode\":\"395010\",\n\"latitude\":\"\",\n\"longitude\":\"\"\n}\nfile:image //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Profile Updated Successfully.\",\n\"cause\": \"\",\n\"data\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsedAPIsController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "verify2faOPT",
    "title": "verify2faOPT",
    "name": "verify2faOPT",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"verify_code\": \"557537\", //compulsory\n\"user_id\": \"557537\", //compulsory\n\"google2fa_secret\": \"557537\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"OTP verified successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_detail\": {\n\"id\": 1,\n\"user_name\": \"admin\",\n\"email_id\": \"admin@gmail.com\",\n\"google2fa_enable\": 1,\n\"google2fa_secret\": \"CY3VRNFBMJBA75EA\",\n\"social_uid\": null,\n\"signup_type\": null,\n\"profile_setup\": 0,\n\"is_active\": 1,\n\"create_time\": \"2017-08-02 12:08:30\",\n\"update_time\": \"2018-10-20 06:11:38\",\n\"attribute1\": null,\n\"attribute2\": null,\n\"attribute3\": null,\n\"attribute4\": null\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/Google2faController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "doLogout",
    "title": "doLogout",
    "name": "doLogout",
    "group": "Common_For_All",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"User have successfully logged out.\",\n\"cause\": \"\",\n\"data\": {\n\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/LoginController.php",
    "groupTitle": "Common_For_All"
  },
  {
    "type": "post",
    "url": "getCatalogBySubCategoryId",
    "title": "getCatalogBySubCategoryId",
    "name": "getCatalogBySubCategoryId",
    "group": "Common_For_All",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 5,\n\"category_name\": \"Independence Day Stickers\",\n\"category_list\": [\n{\n\"catalog_id\": 84,\n\"name\": \"Misc\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d551036b09_catalog_img_1502434576.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d551036b09_catalog_img_1502434576.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d551036b09_catalog_img_1502434576.png\",\n\"is_free\": 0,\n\"is_featured\": 1\n},\n{\n\"catalog_id\": 80,\n\"name\": \"Circle\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 81,\n\"name\": \"Flag\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 82,\n\"name\": \"Map\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 83,\n\"name\": \"Text\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Common_For_All"
  },
  {
    "type": "post",
    "url": "getSubCategoryByCategoryId",
    "title": "getSubCategoryByCategoryId",
    "name": "getSubCategoryByCategoryId",
    "group": "Common_For_All",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"category_id\":1, //compulsory\n \"page\":1, //compulsory\n \"item_count\":100 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Sub categories fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 33,\n\"is_next_page\": true,\n\"category_name\": \"Sticker\",\n\"category_list\": [\n{\n\"sub_category_id\": 66,\n\"category_id\": 2,\n\"name\": \"All Templates\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"is_featured\": 0\n},\n{\n\"sub_category_id\": 97,\n\"category_id\": 2,\n\"name\": \"Brand Maker\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6d33c860e1e_sub_category_img_1550660552.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6d33c860e1e_sub_category_img_1550660552.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6d33c860e1e_sub_category_img_1550660552.jpg\",\n\"is_featured\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Common_For_All"
  },
  {
    "type": "post",
    "url": "appPurchasePayment",
    "title": "appPurchasePayment",
    "name": "appPurchasePayment",
    "group": "Payment_Subscription",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "\"order_info\": {\n\"sub_category_id\": 1,\n\"auto_renewing\": \"false\",\n\"order_id\": \"Null1494322134113\",\n\"package_name\": \"com.optimumbrewlab.bgchanger\",\n\"product_id\": \"com.optimumbrewlab.bgchanger_remove_ads\",\n\"purchase_state\": \"0\",\n\"purchase_time\": \"1494248457019\",\n\"purchase_token\": \"fflngnjoghggeahffeigjcba.AO-J1OwSyrfoOpvnkHeucfplRQno1CKJ40McIPpqioJBjEisg1B8g4bVJgEkeOjZ6-EUMN-M4_r1kbFIkmcXqRuSGRZ3awoOInx0_OMCgecwXLtCBUy8BnMSYuLJhRNg3qZ8d7mVwGaICXFmPwSC9m_4ScsxSHBP0Outfi0fVWU8R7UOZia7flM\",\n\"tot_order_amount\": 2.99\n},\n\"device_info\": {\n\"sub_category_id\": 1,\n\"device_application_version\": \"2.0.2\",\n\"device_carrier\": \"IND AirTel\",\n\"device_country_code\": \"in\",\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en_us\",\n\"device_latitude\": \"\",\n\"device_library_version\": \"2\",\n\"device_local_code\": \"NA\",\n\"device_longitude\": \"\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\",\n\"device_reg_id\": \"eqZf82uRnws:APA91bHl4Mt0y1gTgFXuR63COt-IalksVGHy8Pb9y8JyluqQhdJKUJeOWBINd8fKRBmjnTV47hclfjiRN330vSe1E2l8GJ43O91BqpELhFUWnrjUICmb63XGNwwJXgXdwG8isMdUf3eC\",\n\"device_registration_date\": \"2017-05-09T14:58:54 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"ff505a7a500c931a\",\n\"device_vendor_name\": \"Micromax\",\n\"project_package_name\": \"com.optimumbrewlab.bgchanger\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Payment was successful.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/SubscriptionPaymentController.php",
    "groupTitle": "Payment_Subscription"
  },
  {
    "type": "post",
    "url": "appPurchasePaymentForIOS",
    "title": "appPurchasePaymentForIOS",
    "name": "appPurchasePaymentForIOS",
    "group": "Payment_Subscription",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"order_info\": {\n\"sub_category_id\": 1,\n\"purchase_token\": \"\",\n\"auto_renewing\": \"false\",\n\"order_id\": \"1000000297266758\",\n\"product_id\": \"qrcodescanner_pro\",\n\"tot_order_amount\": 1.99,\n\"package_name\": \"com.optimumbrewlab.bgchanger-\",\n\"purchase_time\": \"1494339022669\",\n\"purchase_state\": \"1\",\n\"receipt_base64_data\": \"MIITyAYJKoZIhvcNAQcCoIITuTCCE7UCAQExCzAJBgUrDgMCGgUAMIIDaQYJKoZIhvcNAQcBoIIDWgSCA1YxggNSMAoCAQgCAQEEAhYAMAoCARQCAQEEAgwAMAsCAQECAQEEAwIBADALAgEDAgEBBAMMATEwCwIBCwIBAQQDAgEAMAsCAQ4CAQEEAwIBZTALAgEPAgEBBAMCAQAwCwIBEAIBAQQDAgEAMAsCARkCAQEEAwIBAzAMAgEKAgEBBAQWAjQrMA0CAQ0CAQEEBQIDAYaiMA0CARMCAQEEBQwDMS4wMA4CAQkCAQEEBgIEUDI0NzAYAgEEAgECBBAof4ZkDhYlqeujnVC+xcQJMBsCAQACAQEEEwwRUHJvZHVjdGlvblNhbmRib3gwHAIBBQIBAQQULONiknNB1uevGZ27L5MrdpsjreAwHgIBDAIBAQQWFhQyMDE3LTA1LTA5VDE0OjEwOjIyWjAeAgESAgEBBBYWFDIwMTMtMDgtMDFUMDc6MDA6MDBaMCcCAQICAQEEHwwdY29tLm9wdGltdW1icmV3bGFiLnFyc2Nhbm5lci0wQwIBBwIBAQQ7Lii0vNA4ku3FOmL5M/IYW3KFXPFDwD2jvsKhN5rAP19RC12LDGlS+Yu2jR3vmJFOLxYJqE5kGX6RJB0wRAIBBgIBAQQ80MJ9BlSudxw63Eke6fYwYmY1IUGzgsrdWLLKjGnvK2YiztpkZI2hOojEaPHPEUo3ybjoXxhNaUOcFToTMIIBVgIBEQIBAQSCAUwxggFIMAsCAgasAgEBBAIWADALAgIGrQIBAQQCDAAwCwICBrACAQEEAhYAMAsCAgayAgEBBAIMADALAgIGswIBAQQCDAAwCwICBrQCAQEEAgwAMAsCAga1AgEBBAIMADALAgIGtgIBAQQCDAAwDAICBqUCAQEEAwIBATAMAgIGqwIBAQQDAgEAMAwCAgauAgEBBAMCAQAwDAICBq8CAQEEAwIBADAMAgIGsQIBAQQDAgEAMBsCAganAgEBBBIMEDEwMDAwMDAyOTcxNDg2NzMwGwICBqkCAQEEEgwQMTAwMDAwMDI5NzE0ODY3MzAcAgIGpgIBAQQTDBFxcmNvZGVzY2FubmVyX3BybzAfAgIGqAIBAQQWFhQyMDE3LTA1LTA5VDA5OjA2OjUwWjAfAgIGqgIBAQQWFhQyMDE3LTA1LTA5VDA5OjA2OjUwWqCCDmUwggV8MIIEZKADAgECAggO61eH554JjTANBgkqhkiG9w0BAQUFADCBljELMAkGA1UEBhMCVVMxEzARBgNVBAoMCkFwcGxlIEluYy4xLDAqBgNVBAsMI0FwcGxlIFdvcmxkd2lkZSBEZXZlbG9wZXIgUmVsYXRpb25zMUQwQgYDVQQDDDtBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9ucyBDZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTAeFw0xNTExMTMwMjE1MDlaFw0yMzAyMDcyMTQ4NDdaMIGJMTcwNQYDVQQDDC5NYWMgQXBwIFN0b3JlIGFuZCBpVHVuZXMgU3RvcmUgUmVjZWlwdCBTaWduaW5nMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczETMBEGA1UECgwKQXBwbGUgSW5jLjELMAkGA1UEBhMCVVMwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQClz4H9JaKBW9aH7SPaMxyO4iPApcQmyz3Gn+xKDVWG/6QC15fKOVRtfX+yVBidxCxScY5ke4LOibpJ1gjltIhxzz9bRi7GxB24A6lYogQ+IXjV27fQjhKNg0xbKmg3k8LyvR7E0qEMSlhSqxLj7d0fmBWQNS3CzBLKjUiB91h4VGvojDE2H0oGDEdU8zeQuLKSiX1fpIVK4cCc4Lqku4KXY/Qrk8H9Pm/KwfU8qY9SGsAlCnYO3v6Z/v/Ca/VbXqxzUUkIVonMQ5DMjoEC0KCXtlyxoWlph5AQaCYmObgdEHOwCl3Fc9DfdjvYLdmIHuPsB8/ijtDT+iZVge/iA0kjAgMBAAGjggHXMIIB0zA/BggrBgEFBQcBAQQzMDEwLwYIKwYBBQUHMAGGI2h0dHA6Ly9vY3NwLmFwcGxlLmNvbS9vY3NwMDMtd3dkcjA0MB0GA1UdDgQWBBSRpJz8xHa3n6CK9E31jzZd7SsEhTAMBgNVHRMBAf8EAjAAMB8GA1UdIwQYMBaAFIgnFwmpthhgi+zruvZHWcVSVKO3MIIBHgYDVR0gBIIBFTCCAREwggENBgoqhkiG92NkBQYBMIH+MIHDBggrBgEFBQcCAjCBtgyBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMDYGCCsGAQUFBwIBFipodHRwOi8vd3d3LmFwcGxlLmNvbS9jZXJ0aWZpY2F0ZWF1dGhvcml0eS8wDgYDVR0PAQH/BAQDAgeAMBAGCiqGSIb3Y2QGCwEEAgUAMA0GCSqGSIb3DQEBBQUAA4IBAQANphvTLj3jWysHbkKWbNPojEMwgl/gXNGNvr0PvRr8JZLbjIXDgFnf4+LXLgUUrA3btrj+/DUufMutF2uOfx/kd7mxZ5W0E16mGYZ2+FogledjjA9z/Ojtxh+umfhlSFyg4Cg6wBA3LbmgBDkfc7nIBf3y3n8aKipuKwH8oCBc2et9J6Yz+PWY4L5E27FMZ/xuCk/J4gao0pfzp45rUaJahHVl0RYEYuPBX/UIqc9o2ZIAycGMs/iNAGS6WGDAfK+PdcppuVsq1h1obphC9UynNxmbzDscehlD86Ntv0hgBgw2kivs3hi1EdotI9CO/KBpnBcbnoB7OUdFMGEvxxOoMIIEIjCCAwqgAwIBAgIIAd68xDltoBAwDQYJKoZIhvcNAQEFBQAwYjELMAkGA1UEBhMCVVMxEzARBgNVBAoTCkFwcGxlIEluYy4xJjAkBgNVBAsTHUFwcGxlIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MRYwFAYDVQQDEw1BcHBsZSBSb290IENBMB4XDTEzMDIwNzIxNDg0N1oXDTIzMDIwNzIxNDg0N1owgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDKOFSmy1aqyCQ5SOmM7uxfuH8mkbw0U3rOfGOAYXdkXqUHI7Y5/lAtFVZYcC1+xG7BSoU+L/DehBqhV8mvexj/avoVEkkVCBmsqtsqMu2WY2hSFT2Miuy/axiV4AOsAX2XBWfODoWVN2rtCbauZ81RZJ/GXNG8V25nNYB2NqSHgW44j9grFU57Jdhav06DwY3Sk9UacbVgnJ0zTlX5ElgMhrgWDcHld0WNUEi6Ky3klIXh6MSdxmilsKP8Z35wugJZS3dCkTm59c3hTO/AO0iMpuUhXf1qarunFjVg0uat80YpyejDi+l5wGphZxWy8P3laLxiX27Pmd3vG2P+kmWrAgMBAAGjgaYwgaMwHQYDVR0OBBYEFIgnFwmpthhgi+zruvZHWcVSVKO3MA8GA1UdEwEB/wQFMAMBAf8wHwYDVR0jBBgwFoAUK9BpR5R2Cf70a40uQKb3R01/CF4wLgYDVR0fBCcwJTAjoCGgH4YdaHR0cDovL2NybC5hcHBsZS5jb20vcm9vdC5jcmwwDgYDVR0PAQH/BAQDAgGGMBAGCiqGSIb3Y2QGAgEEAgUAMA0GCSqGSIb3DQEBBQUAA4IBAQBPz+9Zviz1smwvj+4ThzLoBTWobot9yWkMudkXvHcs1Gfi/ZptOllc34MBvbKuKmFysa/Nw0Uwj6ODDc4dR7Txk4qjdJukw5hyhzs+r0ULklS5MruQGFNrCk4QttkdUGwhgAqJTleMa1s8Pab93vcNIx0LSiaHP7qRkkykGRIZbVf1eliHe2iK5IaMSuviSRSqpd1VAKmuu0swruGgsbwpgOYJd+W+NKIByn/c4grmO7i77LpilfMFY0GCzQ87HUyVpNur+cmV6U/kTecmmYHpvPm0KdIBembhLoz2IYrF+Hjhga6/05Cdqa3zr/04GpZnMBxRpVzscYqCtGwPDBUfMIIEuzCCA6OgAwIBAgIBAjANBgkqhkiG9w0BAQUFADBiMQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEmMCQGA1UECxMdQXBwbGUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkxFjAUBgNVBAMTDUFwcGxlIFJvb3QgQ0EwHhcNMDYwNDI1MjE0MDM2WhcNMzUwMjA5MjE0MDM2WjBiMQswCQYDVQQGEwJVUzETMBEGA1UEChMKQXBwbGUgSW5jLjEmMCQGA1UECxMdQXBwbGUgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkxFjAUBgNVBAMTDUFwcGxlIFJvb3QgQ0EwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDkkakJH5HbHkdQ6wXtXnmELes2oldMVeyLGYne+Uts9QerIjAC6Bg++FAJ039BqJj50cpmnCRrEdCju+QbKsMflZ56DKRHi1vUFjczy8QPTc4UadHJGXL1XQ7Vf1+b8iUDulWPTV0N8WQ1IxVLFVkds5T39pyez1C6wVhQZ48ItCD3y6wsIG9wtj8BMIy3Q88PnT3zK0koGsj+zrW5DtleHNbLPbU6rfQPDgCSC7EhFi501TwN22IWq6NxkkdTVcGvL0Gz+PvjcM3mo0xFfh9Ma1CWQYnEdGILEINBhzOKgbEwWOxaBDKMaLOPHd5lc/9nXmW8Sdh2nzMUZaF3lMktAgMBAAGjggF6MIIBdjAOBgNVHQ8BAf8EBAMCAQYwDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQUK9BpR5R2Cf70a40uQKb3R01/CF4wHwYDVR0jBBgwFoAUK9BpR5R2Cf70a40uQKb3R01/CF4wggERBgNVHSAEggEIMIIBBDCCAQAGCSqGSIb3Y2QFATCB8jAqBggrBgEFBQcCARYeaHR0cHM6Ly93d3cuYXBwbGUuY29tL2FwcGxlY2EvMIHDBggrBgEFBQcCAjCBthqBs1JlbGlhbmNlIG9uIHRoaXMgY2VydGlmaWNhdGUgYnkgYW55IHBhcnR5IGFzc3VtZXMgYWNjZXB0YW5jZSBvZiB0aGUgdGhlbiBhcHBsaWNhYmxlIHN0YW5kYXJkIHRlcm1zIGFuZCBjb25kaXRpb25zIG9mIHVzZSwgY2VydGlmaWNhdGUgcG9saWN5IGFuZCBjZXJ0aWZpY2F0aW9uIHByYWN0aWNlIHN0YXRlbWVudHMuMA0GCSqGSIb3DQEBBQUAA4IBAQBcNplMLXi37Yyb3PN3m/J20ncwT8EfhYOFG5k9RzfyqZtAjizUsZAS2L70c5vu0mQPy3lPNNiiPvl4/2vIB+x9OYOLUyDTOMSxv5pPCmv/K/xZpwUJfBdAVhEedNO3iyM7R6PVbyTi69G3cN8PReEnyvFteO3ntRcXqNx+IjXKJdXZD9Zr1KIkIxH3oayPc4FgxhtbCS+SsvhESPBgOJ4V9T0mZyCKM2r3DYLP3uujL/lTaltkwGMzd/c6ByxW69oPIQ7aunMZT7XZNn/Bh1XZp5m5MkL72NVxnn6hUrcbvZNCJBIqxw8dtk2cXmPIS4AXUKqK1drk/NAJBzewdXUhMYIByzCCAccCAQEwgaMwgZYxCzAJBgNVBAYTAlVTMRMwEQYDVQQKDApBcHBsZSBJbmMuMSwwKgYDVQQLDCNBcHBsZSBXb3JsZHdpZGUgRGV2ZWxvcGVyIFJlbGF0aW9uczFEMEIGA1UEAww7QXBwbGUgV29ybGR3aWRlIERldmVsb3BlciBSZWxhdGlvbnMgQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkCCA7rV4fnngmNMAkGBSsOAwIaBQAwDQYJKoZIhvcNAQEBBQAEggEAJK0IsJTwkBeft6HPP3mlDFXfNLGtu9U8Rb4S6SNtA2cJ4r9zWtcqtbISbQsLY77AWDMnTZcRw6UtGUDqSMOtYTlB5UJYTFdHCvchPbVjHnBLX35aXktUFJaEpkyCbWGp50gHnB1P2eSDG/w8pjvbQavPFp45mWQyWDh8nJYzzPhILmtpEgTDCs0EMK3s6pnQDY4BNuyk7npZ+iUDnLv00b+qCG8UN8t+zw3eNR7XhlithtQs2kwFac5f2PKDMcNtUBu1oQ4cbmorUO8Ssi7EDl+drv6yBnxN/9aSmYMaZ0xv5xPKX0oc7g9Ouh7DqOvmT6ChJ1EHb7URpC6r2nARdQ==\"\n},\n\"device_info\": {\n\"sub_category_id\": 1,\n\"device_udid\": \"8DC1456E-3EBC-43FA-985A-DDAC492418FD\",\n\"device_reg_id\": \"911F5C2F54F94DC6037C1A249B0BC98BFF5B17DF2A1C8CE5A546201A5B8DCF6B\",\n\"device_local_code\": \"en-US\",\n\"device_platform\": \"ios\",\n\"device_resolution\": \"320.0568.0\",\n\"device_library_version\": \"\",\n\"device_carrier\": \"\",\n\"device_os_version\": \"10.0.2\",\n\"device_default_time_zone\": \"Asia/Kolkata\",\n\"device_type\": \"phone\",\n\"project_package_name\": \"com.optimumbrewlab.bgchanger-\",\n\"device_vendor_name\": \"Apple\",\n\"device_model_name\": \"iPhone\",\n\"device_longitude\": \"0.0\",\n\"device_latitude\": \"0.0\",\n\"device_country_code\": \"US\",\n\"device_application_version\": \"1\",\n\"device_language\": \"en-US\",\n\"device_registration_date\": \"2017-05-09 14:10:22 +0000\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Payment was successful.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/SubscriptionPaymentController.php",
    "groupTitle": "Payment_Subscription"
  },
  {
    "type": "post",
    "url": "addQuestionAnswer",
    "title": "addQuestionAnswer",
    "name": "addQuestionAnswer",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question\":\"Test\", //compulsory\n\"answer\":\"<p>Test</p>\", //compulsory\n\"question_type\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question and Answer added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "addQuestionType",
    "title": "addQuestionType",
    "name": "addQuestionType",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"request_data\":{\n\"question_type\":1 //compulsory\n},\n\"file\":\"1.jpg\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question type added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "addYouTubeVideoURL",
    "title": "addYouTubeVideoURL",
    "name": "addYouTubeVideoURL",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"url\":\"https://www.youtube.com/watch?v=E78k_XDjFLA\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video url added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "deleteQuestionAnswer",
    "title": "deleteQuestionAnswer",
    "name": "deleteQuestionAnswer",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question and Answer deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "deleteQuestionType",
    "title": "deleteQuestionType",
    "name": "deleteQuestionType",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_type_id\":7\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question type deleted successfully\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "deleteYouTubeVideoURL",
    "title": "deleteYouTubeVideoURL",
    "name": "deleteYouTubeVideoURL",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"video_id\":9 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getAllQuestionAnswer",
    "title": "getAllQuestionAnswer",
    "name": "getAllQuestionAnswer",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All question and answer fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"question_id\": 5,\n\"question_type\": 1,\n\"question\": \"test\",\n\"answer\": \"<p style=\\\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\\\"><font color=\\\"#333333\\\" face=\\\"Georgia, serif\\\"><span style=\\\"font-size: 17.3333px;\\\">test</span></font></p>\",\n\"create_time\": \"2018-12-26 04:58:34\",\n\"update_time\": \"2018-12-26 04:58:34\"\n},\n{\n\"question_id\": 3,\n\"question_type\": 2,\n\"question\": \"Research the organization\",\n\"answer\": \"<p>test1</p>\",\n\"create_time\": \"2018-12-26 04:25:34\",\n\"update_time\": \"2018-12-26 04:30:49\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getAllQuestionAnswerByTypeForAdmin",
    "title": "getAllQuestionAnswerByTypeForAdmin",
    "name": "getAllQuestionAnswerByTypeForAdmin",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_type_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All question and answer fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"question_id\": 5,\n\"question_type\": 1,\n\"question\": \"test\",\n\"answer\": \"<p style=\\\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\\\"><font color=\\\"#333333\\\" face=\\\"Georgia, serif\\\"><span style=\\\"font-size: 17.3333px;\\\">test</span></font></p>\",\n\"create_time\": \"2018-12-26 04:58:34\",\n\"update_time\": \"2018-12-26 04:58:34\"\n},\n{\n\"question_id\": 2,\n\"question_type\": 1,\n\"question\": \"Research the organization\",\n\"answer\": \"<p>Test</p>\",\n\"create_time\": \"2018-12-26 04:25:03\",\n\"update_time\": \"2018-12-26 04:25:03\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getAllQuestionTypeForAdmin",
    "title": "getAllQuestionTypeForAdmin",
    "name": "getAllQuestionTypeForAdmin",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All question and answer fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"question_type_id\": 1,\n\"question_type\": \"Interview Prep Plan\",\n\"question_type_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c22f822b2c0d_question_type_1545795618.png\",\n\"create_time\": \"2018-11-28 10:38:41\",\n\"update_time\": \"2018-12-26 03:40:19\"\n},\n{\n\"question_type_id\": 2,\n\"question_type\": \"Most Common\",\n\"question_type_image\": \"\",\n\"create_time\": \"2018-11-28 10:38:47\",\n\"update_time\": \"2018-11-28 10:38:47\"\n},\n{\n\"question_type_id\": 3,\n\"question_type\": \"Behavioural\",\n\"question_type_image\": \"\",\n\"create_time\": \"2018-11-28 10:38:51\",\n\"update_time\": \"2018-11-28 10:38:51\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getVideoIdByURL",
    "title": "getVideoIdByURL",
    "name": "getVideoIdByURL",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"url\":\"https://www.youtube.com/watch?v=yBtMwyQFXwA\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video url added successfully.\",\n\"cause\": \"\",\n\"data\": \"yBtMwyQFXwA\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getYouTubeVideoForInterviewForAdmin",
    "title": "getYouTubeVideoForInterviewForAdmin",
    "name": "getYouTubeVideoForInterviewForAdmin",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video fatched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"video_id\": 9,\n\"youtube_video_id\": \"E78k_XDjFLA\",\n\"title\": \"How to act in an interview\",\n\"channel_name\": \"LoquaCommunications\",\n\"url\": \"https://www.youtube.com/watch?v=E78k_XDjFLA\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/E78k_XDjFLA/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2009-10-07 19:40:34\"\n},\n{\n\"video_id\": 8,\n\"youtube_video_id\": \"kayOhGRcNt4\",\n\"title\": \"Tell Me About Yourself - A Good Answer to This Interview Question\",\n\"channel_name\": \"Linda Raynier\",\n\"url\": \"https://www.youtube.com/watch?v=kayOhGRcNt4\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/kayOhGRcNt4/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2016-12-14 15:12:37\"\n},\n{\n\"video_id\": 7,\n\"youtube_video_id\": \"BkL98JHAO_w\",\n\"title\": \"Mock Job Interview Questions and Tips for a Successful Interview\",\n\"channel_name\": \"Virginia Western Community College\",\n\"url\": \"https://www.youtube.com/watch?v=BkL98JHAO_w\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/BkL98JHAO_w/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2009-09-25 20:36:08\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "searchQuestionAnswer",
    "title": "searchQuestionAnswer",
    "name": "searchQuestionAnswer",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"question_type\":1,\n\"search_query\":\"Test\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question and answer fetched successfully.\",\n\"cause\": \"\",\n\"response\": {\n\"total_record\": 1,\n\"is_next_page\": false,\n\"result\": [\n{\n\"question_id\": 5,\n\"question_type\": 1,\n\"question\": \"test\",\n\"answer\": \"<p style=\\\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\\\"><font color=\\\"#333333\\\" face=\\\"Georgia, serif\\\"><span style=\\\"font-size: 17.3333px;\\\">test</span></font></p>\",\n\"create_time\": \"2018-12-26 04:58:34\",\n\"update_time\": \"2018-12-26 04:58:34\",\n\"search_text\": 0.36247622966766\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "updateQuestionAnswer",
    "title": "updateQuestionAnswer",
    "name": "updateQuestionAnswer",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_id\":1, //compulsory\n\"question\":\"Test\", //compulsory\n\"answer\":\"<p>Test</p>\", //compulsory\n\"question_type\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question and Answer updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "updateQuestionType",
    "title": "updateQuestionType",
    "name": "updateQuestionType",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"request_data\":{\n\"question_type_id\":1, //compulsory\n\"question_type\":1 //compulsory\n},\n\"file\":\"1.jpg\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question type updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "updateYouTubeVideoURL",
    "title": "updateYouTubeVideoURL",
    "name": "updateYouTubeVideoURL",
    "group": "Resume_Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"video_id\":1, //compulsory\n\"title\":\"How to Interview for a Job in American English, part 1/5 Test\",\n\"url\":\"https://www.youtube.com/watch?v=yBtMwyQFXwA test\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_Admin"
  },
  {
    "type": "post",
    "url": "getAllQuestionAnswerByType",
    "title": "getAllQuestionAnswerByType",
    "name": "getAllQuestionAnswerByType",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_type_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All question and answer fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"question_id\": 5,\n\"question_type\": 1,\n\"question\": \"test\",\n\"answer\": \"<p style=\\\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\\\"><font color=\\\"#333333\\\" face=\\\"Georgia, serif\\\"><span style=\\\"font-size: 17.3333px;\\\">test</span></font></p>\",\n\"create_time\": \"2018-12-26 04:58:34\",\n\"update_time\": \"2018-12-26 04:58:34\"\n},\n{\n\"question_id\": 2,\n\"question_type\": 1,\n\"question\": \"Research the organization\",\n\"answer\": \"<p>Test</p>\",\n\"create_time\": \"2018-12-26 04:25:03\",\n\"update_time\": \"2018-12-26 04:25:03\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "getAllQuestionType",
    "title": "getAllQuestionType",
    "name": "getAllQuestionType",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All question and answer fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"question_type_id\": 1,\n\"question_type\": \"Interview Prep Plan\",\n\"question_type_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c22f822b2c0d_question_type_1545795618.png\",\n\"create_time\": \"2018-11-28 10:38:41\",\n\"update_time\": \"2018-12-26 03:40:19\"\n},\n{\n\"question_type_id\": 2,\n\"question_type\": \"Most Common\",\n\"question_type_image\": \"\",\n\"create_time\": \"2018-11-28 10:38:47\",\n\"update_time\": \"2018-11-28 10:38:47\"\n},\n{\n\"question_type_id\": 3,\n\"question_type\": \"Behavioural\",\n\"question_type_image\": \"\",\n\"create_time\": \"2018-11-28 10:38:51\",\n\"update_time\": \"2018-11-28 10:38:51\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "getFeedFromTwitter",
    "title": "getFeedFromTwitter",
    "name": "getFeedFromTwitter",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Twitter post fatched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 1198,\n\"total_pages\": 60,\n\"is_next_page\": true,\n\"result\": [\n{\n\"id\": 1077628187000094700,\n\"created_at\": \"2018-12-25 18:12:35\",\n\"text\": \"Tech startups, ecommerce companies to step up hiring in 2019 #Jobs <a href=\\\"https://t.co/nJQY9q3krl\\\" target=\\\"_blank\\\">https://t.co/nJQY9q3krl</a>\",\n\"favorite_count\": 0,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/795539118172160001/IbZPUHK9_400x400.jpg\",\n\"account_url\": \"https://twitter.com/ETJobNews\",\n\"media_url_https\": \"\",\n\"post_type\": 1,\n\"video_url\": \"\"\n},\n{\n\"id\": 1077625329257201700,\n\"created_at\": \"2018-12-25 18:01:14\",\n\"text\": \"Are you a talented Housekeeper in #Winchester? We want you on our team! #jobs <a href=\\\"https://t.co/TVZ2u7wjYi\\\" target=\\\"_blank\\\">https://t.co/TVZ2u7wjYi</a> <a href=\\\"https://t.co/2f2DEskXmy\\\" target=\\\"_blank\\\">https://t.co/2f2DEskXmy</a>\",\n\"favorite_count\": 1,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/1056994307658432512/aGmzhHz4_400x400.jpg\",\n\"account_url\": \"https://twitter.com/MonsterJobs\",\n\"media_url_https\": \"https://pbs.twimg.com/media/DvR-hoaWsAErDTT.jpg\",\n\"post_type\": 2,\n\"video_url\": \"\"\n}\n],\n\"is_cache\": 1\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/NewsController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "getHomePageDetail",
    "title": "getHomePageDetail",
    "name": "getHomePageDetail",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1, //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Home page detail fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"template\": [\n{\n\"json_id\": 2488,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5b81064f6a5d3_json_image_1535182415.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-10-02 11:05:56\"\n},\n{\n\"json_id\": 711,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a953eafef82b_json_image_1519730351.png\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-10-02 10:48:43\"\n},\n{\n\"json_id\": 3105,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5b9787d634af2_json_image_1536657366.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"height\": 600,\n\"width\": 400,\n\"updated_at\": \"2018-09-11 09:16:07\"\n},\n{\n\"json_id\": 732,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a9650b07f43d_json_image_1519800496.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-09-05 05:20:37\"\n},\n{\n\"json_id\": 731,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a9650930c5a0_json_image_1519800467.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-09-05 05:20:33\"\n},\n{\n\"json_id\": 728,\n\"sample_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/webp_original/5a965023e6c56_json_image_1519800355.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-09-05 05:20:24\"\n}\n],\n\"video\": [\n{\n\"video_id\": 20,\n\"youtube_video_id\": \"d6uzZqkcsa8\",\n\"title\": \"How To Interview Candidates For A Job\",\n\"channel_name\": \"Videojug\",\n\"url\": \"https://www.youtube.com/watch?v=d6uzZqkcsa8\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/d6uzZqkcsa8/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2011-04-12 13:51:31\"\n},\n{\n\"video_id\": 19,\n\"youtube_video_id\": \"htBDNsunGCY\",\n\"title\": \"How to Conduct an Interview\",\n\"channel_name\": \"HR360Inc\",\n\"url\": \"https://www.youtube.com/watch?v=htBDNsunGCY\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/htBDNsunGCY/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2014-06-09 15:08:05\"\n},\n{\n\"video_id\": 18,\n\"youtube_video_id\": \"2kNIlIrocrU\",\n\"title\": \"Hiring tutorial: Writing effective behavioral interview questions | lynda.com\",\n\"channel_name\": \"LinkedIn Learning\",\n\"url\": \"https://www.youtube.com/watch?v=2kNIlIrocrU\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/2kNIlIrocrU/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2013-06-18 21:54:24\"\n},\n{\n\"video_id\": 17,\n\"youtube_video_id\": \"5NVYg2HNAdA\",\n\"title\": \"\\\"Why Should We Hire You?\\\" How to Answer this Interview Question\",\n\"channel_name\": \"Fisher College of Business\",\n\"url\": \"https://www.youtube.com/watch?v=5NVYg2HNAdA\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/5NVYg2HNAdA/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2012-03-06 13:50:47\"\n},\n{\n\"video_id\": 16,\n\"youtube_video_id\": \"VFTNOF77bMs\",\n\"title\": \"Interview questions and answers\",\n\"channel_name\": \"JobTestPrep\",\n\"url\": \"https://www.youtube.com/watch?v=VFTNOF77bMs\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/VFTNOF77bMs/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2011-12-05 09:03:39\"\n},\n{\n\"video_id\": 15,\n\"youtube_video_id\": \"PCWVi5pAa30\",\n\"title\": \"7 body language tips to impress at your next job interview\",\n\"channel_name\": \"Cognitive Group Microsoft Talent Solutions\",\n\"url\": \"https://www.youtube.com/watch?v=PCWVi5pAa30\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/PCWVi5pAa30/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2016-03-16 07:56:32\"\n}\n],\n\"job_news\": [\n{\n\"id\": 1072922200905150500,\n\"created_at\": \"2018-12-12 18:32:41\",\n\"text\": \"RT <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/TwitterAPI\\\" target=\\\"_blank\\\">@TwitterAPI</a>: All app management is unifying on <a href=\\\"https://t.co/EfJLLFaLkk!\\\" target=\\\"_blank\\\">https://t.co/EfJLLFaLkk!</a> Beginning today: \\n\\n<a href=\\\"https://t.co/PCwEGWityX\\\" target=\\\"_blank\\\">https://t.co/PCwEGWityX</a>\\n&gt; will be redirected?,\n\"favorite_count\": 0,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"\",\n\"post_type\": 1,\n\"video_url\": \"\"\n},\n{\n\"id\": 1070059276213702700,\n\"created_at\": \"2018-12-04 20:56:26\",\n\"text\": \"Celebrating the developer success story of <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/UnionMetrics\\\" target=\\\"_blank\\\">@UnionMetrics</a> platform whose underlying technology is built upon Twitter�<a href=\\\"https://t.co/lxA6ePkTMj\\\" target=\\\"_blank\\\">https://t.co/lxA6ePkTMj</a>\",\n\"favorite_count\": 48,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"\",\n\"post_type\": 1,\n\"video_url\": \"\"\n},\n{\n\"id\": 1067094924124872700,\n\"created_at\": \"2018-11-26 16:37:10\",\n\"text\": \"Just getting started with Twitter APIs? Find out what you need in order to build an app. Watch this video! <a href=\\\"https://t.co/Hg8nkfoizN\\\" target=\\\"_blank\\\">https://t.co/Hg8nkfoizN</a>\",\n\"favorite_count\": 490,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"https://pbs.twimg.com/media/DsZp7igVYAAyDHB.jpg\",\n\"post_type\": 3,\n\"video_url\": \"https://video.twimg.com/amplify_video/1064638969197977600/vid/1280x720/C1utUYBYhJ_4lwaq.mp4?tag=8\"\n},\n{\n\"id\": 1058408022936977400,\n\"created_at\": \"2018-11-02 17:18:31\",\n\"text\": \"RT <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/harmophone\\\" target=\\\"_blank\\\">@harmophone</a>: \\\"The innovative crowdsourcing that the Tagboard, Twitter and TEGNA collaboration enables is surfacing locally relevant conv?,\n\"favorite_count\": 0,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"\",\n\"post_type\": 1,\n\"video_url\": \"\"\n},\n{\n\"id\": 1054884245578035200,\n\"created_at\": \"2018-10-23 23:56:17\",\n\"text\": \"RT <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/andypiper\\\" target=\\\"_blank\\\">@andypiper</a>: My coworker <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/jessicagarson\\\" target=\\\"_blank\\\">@jessicagarson</a> rocking Jupyter and Postman in her #TapIntoTwitterNYC demo! <a href=\\\"https://t.co/yuF4q2Czed\\\" target=\\\"_blank\\\">https://t.co/yuF4q2Czed</a>\",\n\"favorite_count\": 0,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"https://pbs.twimg.com/media/DqOrzHJWsAAWDGE.jpg\",\n\"post_type\": 2,\n\"video_url\": \"\"\n},\n{\n\"id\": 1054884091227594800,\n\"created_at\": \"2018-10-23 23:55:40\",\n\"text\": \"RT <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/dgrreen\\\" target=\\\"_blank\\\">@dgrreen</a>: #TapintoTwitterNYC First up all the way from <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/TwitterBoulder\\\" target=\\\"_blank\\\">@TwitterBoulder</a>  <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/AdventureSteady\\\" target=\\\"_blank\\\">@AdventureSteady</a> with updates on how <a class=\\\"tweet-author\\\" href=\\\"https://twitter.com/TwitterDev\\\" target=\\\"_blank\\\">@TwitterDev</a> is protecting u?,\n\"favorite_count\": 0,\n\"profile_image_url\": \"http://pbs.twimg.com/profile_images/880136122604507136/xHrnqf1T_400x400.jpg\",\n\"account_url\": \"https://twitter.com/TwitterDev\",\n\"media_url_https\": \"\",\n\"post_type\": 1,\n\"video_url\": \"\"\n}\n],\n\"interview_que_ans\": [\n{\n\"question_type_id\": 1,\n\"question_type\": \"Interview Prep Plan\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c2056193c6c1_question_type_1545623065.png\",\n\"create_time\": \"2018-11-28 10:38:41\",\n\"update_time\": \"2018-12-24 03:44:26\"\n},\n{\n\"question_type_id\": 2,\n\"question_type\": \"Most Common\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c2055a09edd4_question_type_1545622944.png\",\n\"create_time\": \"2018-11-28 10:38:47\",\n\"update_time\": \"2018-12-24 03:42:25\"\n},\n{\n\"question_type_id\": 3,\n\"question_type\": \"Behavioural\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205563bc996_question_type_1545622883.png\",\n\"create_time\": \"2018-11-28 10:38:51\",\n\"update_time\": \"2018-12-24 03:41:24\"\n},\n{\n\"question_type_id\": 4,\n\"question_type\": \"Resume writing\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c20554a6c98c_question_type_1545622858.png\",\n\"create_time\": \"2018-11-28 10:38:59\",\n\"update_time\": \"2018-12-24 03:40:59\"\n},\n{\n\"question_type_id\": 5,\n\"question_type\": \"Technical questions\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205529e152d_question_type_1545622825.png\",\n\"create_time\": \"2018-11-28 10:39:04\",\n\"update_time\": \"2018-12-24 03:40:26\"\n},\n{\n\"question_type_id\": 6,\n\"question_type\": \"Personality\",\n\"question_type_image\": \"http://192.168.0.114/photo_editor_lab_backend/image_bucket/compressed/5c205504ef2a1_question_type_1545622788.png\",\n\"create_time\": \"2018-11-28 10:39:07\",\n\"update_time\": \"2018-12-24 03:39:49\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "getYouTubeVideoForInterview",
    "title": "getYouTubeVideoForInterview",
    "name": "getYouTubeVideoForInterview",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Video fatched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"video_id\": 9,\n\"youtube_video_id\": \"E78k_XDjFLA\",\n\"title\": \"How to act in an interview\",\n\"channel_name\": \"LoquaCommunications\",\n\"url\": \"https://www.youtube.com/watch?v=E78k_XDjFLA\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/E78k_XDjFLA/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2009-10-07 19:40:34\"\n},\n{\n\"video_id\": 8,\n\"youtube_video_id\": \"kayOhGRcNt4\",\n\"title\": \"Tell Me About Yourself - A Good Answer to This Interview Question\",\n\"channel_name\": \"Linda Raynier\",\n\"url\": \"https://www.youtube.com/watch?v=kayOhGRcNt4\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/kayOhGRcNt4/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2016-12-14 15:12:37\"\n},\n{\n\"video_id\": 7,\n\"youtube_video_id\": \"BkL98JHAO_w\",\n\"title\": \"Mock Job Interview Questions and Tips for a Successful Interview\",\n\"channel_name\": \"Virginia Western Community College\",\n\"url\": \"https://www.youtube.com/watch?v=BkL98JHAO_w\",\n\"thumbnail_url\": \"https://i.ytimg.com/vi/BkL98JHAO_w/hqdefault.jpg\",\n\"thumbnail_width\": 480,\n\"thumbnail_height\": 360,\n\"published_at\": \"2009-09-25 20:36:08\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/VideoController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "jobMultiSearchByUser",
    "title": "jobMultiSearchByUser",
    "name": "jobMultiSearchByUser",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"description\":\"Engineering\", //compulsory\n\"location\":\"chicago, il\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Job fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 104,\n\"total_pages\": 3,\n\"is_next_page\": true,\n\"result\": [\n{\n\"sourceId\": \"110671270\",\n\"company\": \"ExxonMobil\",\n\"company_logo\": \"\",\n\"company_url\": \"\",\n\"employmentType\": \"\",\n\"location\": \"\",\n\"source\": \"Careercast\",\n\"query\": \"engineering\",\n\"title\": \"Electrical Engineer\",\n\"job_name\": \"Electrical Engineer\",\n\"url\": \"http://jobs.blackenterprise.com/jobs/electrical-engineer-singapore-01-238510-110671270-d?rsite=careercast&rgroup=1&clientid=blackent&widget=1&type=job&\",\n\"created_at\": \"2018-12-24 09:00:00.000000\"\n},\n{\n\"sourceId\": \"109573702\",\n\"company\": \"Georgia Tech Research Institute (GTRI)\",\n\"company_logo\": \"https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg\",\n\"company_url\": \"/jobs/georgia-tech-research-institute-gtri-1096434-cd\",\n\"employmentType\": \"\",\n\"location\": \"Atlanta, GA\",\n\"source\": \"Ieee\",\n\"query\": \"engineering\",\n\"title\": \"Electronic Warfare and Avionics Software Engineer - ELSYS\",\n\"job_name\": \"Electronic Warfare and Avionics Software Engineer - ELSYS\",\n\"url\": \"https://jobs.ieee.org/jobs/electronic-warfare-and-avionics-software-engineer-elsys-atlanta-ga-109573702-d?widget=1&type=job&\",\n\"created_at\": \"2018-12-24 06:00:00.000000\"\n},\n{\n\"sourceId\": \"110122113\",\n\"company\": \"Georgia Tech Research Institute (GTRI)\",\n\"company_logo\": \"https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg\",\n\"company_url\": \"/jobs/georgia-tech-research-institute-gtri-1096434-cd\",\n\"employmentType\": \"\",\n\"location\": \"Huntsville, AL\",\n\"source\": \"Ieee\",\n\"query\": \"engineering\",\n\"title\": \"Radar Systems Engineer - Huntsville, AL - SEAL\",\n\"job_name\": \"Radar Systems Engineer - Huntsville, AL - SEAL\",\n\"url\": \"https://jobs.ieee.org/jobs/radar-systems-engineer-huntsville-al-seal-huntsville-al-110122113-d?widget=1&type=job&\",\n\"created_at\": \"2018-12-24 06:00:00.000000\"\n},\n{\n\"sourceId\": \"110529248\",\n\"company\": \"Georgia Tech Research Institute (GTRI)\",\n\"company_logo\": \"https://secure.adicio.com/squisher.php?u=https%3A%2F%2Fslb.adicio.com%2Ffiles%2Fys-c-01%2F2017-07%2F27%2F13%2F37%2Fweb_597a4f033826597a4f039f910.jpg\",\n\"company_url\": \"/jobs/georgia-tech-research-institute-gtri-1096434-cd\",\n\"employmentType\": \"\",\n\"location\": \"Smyrna, GA\",\n\"source\": \"Ieee\",\n\"query\": \"engineering\",\n\"title\": \"Algorithm Developer - SEAL\",\n\"job_name\": \"Algorithm Developer - SEAL\",\n\"url\": \"https://jobs.ieee.org/jobs/algorithm-developer-seal-smyrna-ga-110529248-d?widget=1&type=job&\",\n\"created_at\": \"2018-12-24 06:00:00.000000\"\n}\n],\n\"is_cache\": 0\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "jobMultiSearchByUserIndividually",
    "title": "jobMultiSearchByUserIndividually",
    "name": "jobMultiSearchByUserIndividually",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"description\":\"Engineering\", //compulsory\n\"provider\": \"Github\", //?areercast??ice??ithub??ovt??eee??obinventory??onster??tackoverflow?     * \"location\":\"chicago, il\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Job fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 1,\n\"result\": [\n{\n\"sourceId\": \"c307e4ca-d6a6-11e8-8f6e-f00ef74f7cb0\",\n\"company\": \"Squirro\",\n\"company_logo\": \"https://jobs.github.com/rails/active_storage/blobs/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBdFJYIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--f57cd599eb0f28cd5bf62d1214102e55ef446841/728d2587-0eff-4cf3-bfc5-26581e0d58c2\",\n\"company_url\": \"https://www.squirro.com\",\n\"employmentType\": \"\",\n\"location\": \"Zurich\",\n\"source\": \"Github\",\n\"query\": \"Engineering\",\n\"title\": \"Senior Python Engineer\",\n\"job_name\": \"Senior Python Engineer\",\n\"url\": \"https://jobs.github.com/positions/c307e4ca-d6a6-11e8-8f6e-f00ef74f7cb0\",\n\"created_at\": \"2018-10-23 09:36:02.000000\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "searchQuestionAnswer",
    "title": "searchQuestionAnswer",
    "name": "searchQuestionAnswer",
    "group": "Resume_User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"question_type\":1,\n\"search_query\":\"Test\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question and answer fetched successfully.\",\n\"cause\": \"\",\n\"response\": {\n\"total_record\": 1,\n\"is_next_page\": false,\n\"result\": [\n{\n\"question_id\": 5,\n\"question_type\": 1,\n\"question\": \"test\",\n\"answer\": \"<p style=\\\"margin: 0cm 0cm 15pt; line-height: 19.2pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;\\\"><font color=\\\"#333333\\\" face=\\\"Georgia, serif\\\"><span style=\\\"font-size: 17.3333px;\\\">test</span></font></p>\",\n\"create_time\": \"2018-12-26 04:58:34\",\n\"update_time\": \"2018-12-26 04:58:34\",\n\"search_text\": 0.36247622966766\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/QnAController.php",
    "groupTitle": "Resume_User"
  },
  {
    "type": "post",
    "url": "addServerUrl",
    "title": "addServerUrl",
    "name": "addServerUrl",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"server_url\":\"http://192.168.0.113/photo_editor_lab_backend\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Server url added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "deleteServerUrl",
    "title": "deleteServerUrl",
    "name": "deleteServerUrl",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"server_url_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"URL deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getAllServerUrls",
    "title": "getAllServerUrls",
    "name": "getAllServerUrls",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All urls fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"result\": [\n{\n\"server_url_id\": 1,\n\"server_url\": \"http://localhost/photo_editor_lab_backend\",\n\"api_url\": \"http://localhost/photo_editor_lab_backend/api/public/api/\"\n},\n{\n\"server_url_id\": 2,\n\"server_url\": \"http://192.168.0.113/photo_editor_lab_backend_v1\",\n\"api_url\": \"http://192.168.0.113/photo_editor_lab_backend_v1/api/public/api/\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryByAdmin",
    "title": "getSummaryByAdmin",
    "name": "getSummaryByAdmin",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 33,\n\"result\": [\n{\n\"sub_category_id\": 20,\n\"category_id\": 2,\n\"name\": \"Independence Day Stickers\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"no_of_catalogs\": 7,\n\"content_count\": 81,\n\"free_content\": 6,\n\"paid_content\": 75,\n\"is_featured\": 10,\n\"last_uploaded_date\": \"2018-03-10 07:02:54\",\n\"is_active\": 1,\n\"last_uploaded_count\": 6\n},\n{\n\"sub_category_id\": 28,\n\"category_id\": 2,\n\"name\": \"Selfie With Ganesha Stickers\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/59957acc474a9_category_img_1502968524.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/59957acc474a9_category_img_1502968524.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/59957acc474a9_category_img_1502968524.png\",\n\"no_of_catalogs\": 5,\n\"content_count\": 9,\n\"free_content\": 0,\n\"paid_content\": 9,\n\"is_featured\": 10,\n\"last_uploaded_date\": \"2017-08-18 05:18:33\",\n\"is_active\": 1,\n\"last_uploaded_count\": 5\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryByDateRange",
    "title": "getSummaryByDateRange",
    "name": "getSummaryByDateRange",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"category_id\":2, //compulsory\n\"sub_category_id\":66, //compulsory\n\"from_date\":\"2018-01-01\", //compulsory yy-mm-dd\n\"to_date\":\"2019-05-06\", //compulsory\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"date\",\n\"order_type\":\"desc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 59,\n\"is_next_page\": true,\n\"result\": [\n{\n\"date\": \"2018-01-09\",\n\"uploaded_content_count\": 86\n},\n{\n\"date\": \"2018-03-16\",\n\"uploaded_content_count\": 50\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryDetailFromDiffServer",
    "title": "getSummaryDetailFromDiffServer",
    "name": "getSummaryDetailFromDiffServer",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"api_url\":\"http://192.168.0.113/photo_editor_lab_backend_v1/api/public/api/\", //compulsory\n\"category_id\":2, //compulsory\n\"sub_category_id\":66, //compulsory\n\"from_date\":\"2018-01-01\", //compulsory\n\"to_date\":\"2019-05-06\", //compulsory\n\"page\":2, //compulsory\n\"item_count\":2, //compulsory\n\"order_by\":\"date\",\n\"order_type\":\"desc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary details fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 59,\n\"is_next_page\": true,\n\"result\": [\n{\n\"date\": \"2018-09-11\",\n\"uploaded_content_count\": 1\n},\n{\n\"date\": \"2018-09-07\",\n\"uploaded_content_count\": 2\n}\n]\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryOfAllServersByAdmin",
    "title": "getSummaryOfAllServersByAdmin",
    "name": "getSummaryOfAllServersByAdmin",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"summary_of_all_servers\": [\n{\n\"total_record\": 33,\n\"result\": [\n{\n\"sub_category_id\": 20,\n\"category_id\": 2,\n\"name\": \"Independence Day Stickers\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/598d56c20e5bf_sub_category_img_1502435010.png\",\n\"no_of_catalogs\": 7,\n\"content_count\": 81,\n\"free_content\": 6,\n\"paid_content\": 75,\n\"last_uploaded_date\": \"2018-03-10 07:02:54\",\n\"is_active\": 1,\n\"last_uploaded_count\": 6\n}\n],\n\"server_url\": \"localhost\",\n\"api_url\": \"http://localhost/photo_editor_lab_backend/api/public/api/\"\n},\n{\n\"total_record\": 33,\n\"result\": [\n{\n\"sub_category_id\": 95,\n\"category_id\": 2,\n\"name\": \"Test\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c4ac74046e7a_sub_category_img_1548404544.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c4ac74046e7a_sub_category_img_1548404544.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c4ac74046e7a_sub_category_img_1548404544.jpg\",\n\"no_of_catalogs\": 2,\n\"content_count\": 19,\n\"free_content\": 0,\n\"paid_content\": 19,\n\"last_uploaded_date\": \"2019-01-25 09:42:17\",\n\"is_active\": 1,\n\"last_uploaded_count\": 19\n}\n],\n\"server_url\": \"192.168.0.113\",\n\"api_url\": \"http://192.168.0.113/photo_editor_lab_backend/api/public/api/\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryOfCatalogsByDateRange",
    "title": "getSummaryOfCatalogsByDateRange",
    "name": "getSummaryOfCatalogsByDateRange",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":66, //compulsory\n\"from_date\":\"2018-01-01\", //compulsory yy-mm-dd\n\"to_date\":\"2019-05-06\", //compulsory\n\"order_by\":\"catalog_name\",\n\"order_type\":\"desc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 7,\n\"result\": [\n{\n\"catalog_name\": \"Branding\",\n\"content_count\": 82,\n\"last_uploaded_date\": \"2019-03-20 07:39:33\"\n},\n{\n\"catalog_name\": \"Birthday\",\n\"content_count\": 73,\n\"last_uploaded_date\": \"2019-03-20 10:11:52\"\n}\n]\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryOfCatalogsFromDiffServer",
    "title": "getSummaryOfCatalogsFromDiffServer",
    "name": "getSummaryOfCatalogsFromDiffServer",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"api_url\":\"http://192.168.0.113/photo_editor_lab_backend/api/public/api/\", //compulsory\n\"sub_category_id\":66, //compulsory\n\"from_date\":\"2019-03-21\", //compulsory\n\"to_date\":\"2019-05-06\", //compulsory\n\"order_by\":\"last_uploaded_date\",\n\"order_type\":\"desc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary details fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 3,\n\"result\": [\n{\n\"catalog_name\": \"Branding\",\n\"content_count\": 5,\n\"last_uploaded_date\": \"2019-03-22 06:29:35\"\n},\n{\n\"catalog_name\": \"Birthday\",\n\"content_count\": 18,\n\"last_uploaded_date\": \"2019-03-22 06:03:02\"\n}\n]\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "getSummaryOfIndividualServerByAdmin",
    "title": "getSummaryOfIndividualServerByAdmin",
    "name": "getSummaryOfIndividualServerByAdmin",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"api_url\":\"http://localhost/photo_editor_lab_backend/api/public/api/\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Summary fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 4,\n\"result\": [\n{\n\"sub_category_id\": 66,\n\"category_id\": 2,\n\"name\": \"All Templates\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c85fb452c3d4_sub_category_img_1552284485.jpg\",\n\"no_of_catalogs\": 7,\n\"content_count\": 650,\n\"free_content\": 650,\n\"paid_content\": 0,\n\"is_featured\": 1,\n\"last_uploaded_date\": \"2019-03-25 12:22:22\",\n\"is_active\": 1,\n\"last_uploaded_count\": 70\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "updateServerUrl",
    "title": "updateServerUrl",
    "name": "updateServerUrl",
    "group": "Statistics__admin_",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"server_url_id\":1, //compulsory\n\"server_url\":\"http://192.168.0.113/photo_editor_lab_backend\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Server url updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/AdminController.php",
    "groupTitle": "Statistics__admin_"
  },
  {
    "type": "post",
    "url": "clearBadgeCountData",
    "title": "clearBadgeCountData",
    "name": "clearBadgeCountData",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{ }",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"device_reg_id\":\"e12f306fb34ca680aa07d02f44842717ceaf4b35176599a4967017af6b75e247\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Badge count clear successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/NotificationController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "doLoginForGuest",
    "title": "doLoginForGuest",
    "name": "doLoginForGuest",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Body:",
          "content": "{}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login Success.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/LoginController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getAdvertiseServerIdForUser",
    "title": "getAdvertiseServerIdForUser",
    "name": "getAdvertiseServerIdForUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1 //compulsory\n\"device_platform\":1 //compulsory 1=ios, 2=android\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise server id fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"advertise_category_id\": 3,\n\"advertise_category\": \"Rewarded Video\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:07:07\",\n\"update_time\": \"2018-07-16 09:07:07\",\n\"server_id_list\": [\n{\n\"sub_category_advertise_server_id\": 14,\n\"advertise_category_id\": 3,\n\"sub_category_id\": 66,\n\"server_id\": \"test Rewarded Video ID 0\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 13:35:49\",\n\"update_time\": \"2018-07-16 13:42:45\"\n}\n]\n},\n{\n\"advertise_category_id\": 1,\n\"advertise_category\": \"Banner\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\",\n\"server_id_list\": [\n{\n\"sub_category_advertise_server_id\": 16,\n\"advertise_category_id\": 1,\n\"sub_category_id\": 66,\n\"server_id\": \"test Banner ID 2\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 13:38:24\",\n\"update_time\": \"2018-07-16 13:38:24\"\n}\n]\n},\n{\n\"advertise_category_id\": 2,\n\"advertise_category\": \"Intertial\",\n\"is_active\": 1,\n\"create_time\": \"2018-07-16 09:06:47\",\n\"update_time\": \"2018-07-16 09:06:47\",\n\"server_id_list\": []\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getAllFontsByCatalogId",
    "title": "getAllFontsByCatalogId",
    "name": "getAllFontsByCatalogId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":328 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Fonts fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"font_id\": 80,\n\"catalog_id\": 333,\n\"font_name\": \"Shonar Bangla Bold\",\n\"font_file\": \"Shonar-Bold.ttf\",\n\"font_url\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/Shonar-Bold.ttf\",\n\"ios_font_name\": \"ShonarBangla-Bold\"\n},\n{\n\"font_id\": 79,\n\"catalog_id\": 333,\n\"font_name\": \"Shonar Bangla\",\n\"font_file\": \"Shonar.ttf\",\n\"font_url\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/fonts/Shonar.ttf\",\n\"ios_font_name\": \"ShonarBangla\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getAllSamplesWithWebp",
    "title": "getAllSamplesWithWebp",
    "name": "getAllSamplesWithWebp",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory\n\"catalog_id\": 398,\n\"sub_category_id\": 97\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Samples fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 4,\n\"result\": [\n{\n\"json_id\": 3387,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7d03b31ef_json_image_1550810371.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 100,\n\"width\": 320,\n\"search_category\": \"banners\",\n\"original_img_height\": 100,\n\"original_img_width\": 320,\n\"updated_at\": \"2019-03-01 04:59:31\"\n},\n{\n\"json_id\": 3388,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7e1599291_json_image_1550810645.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 408,\n\"width\": 528,\n\"search_category\": \"Brochures\",\n\"original_img_height\": 816,\n\"original_img_width\": 1056,\n\"updated_at\": \"2019-03-01 04:59:04\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getBackgroundCatalogBySubCategoryId",
    "title": "getBackgroundCatalogBySubCategoryId",
    "name": "getBackgroundCatalogBySubCategoryId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 5,\n\"category_name\": \"Independence Day Stickers\",\n\"category_list\": [\n{\n\"catalog_id\": 80,\n\"name\": \"Circle\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 81,\n\"name\": \"Flag\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 82,\n\"name\": \"Map\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 83,\n\"name\": \"Text\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCatalogBySubCategoryIdWithLastSyncTime",
    "title": "getCatalogBySubCategoryIdWithLastSyncTime",
    "name": "getCatalogBySubCategoryIdWithLastSyncTime",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":51,\n\"last_sync_time\":\"0\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"last_sync_time\": \"2017-11-28 06:55:14\",\n\"category_list\": [\n{\n\"catalog_id\": 168,\n\"name\": \"Business Card Catalog2\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"catalog_id\": 167,\n\"name\": \"Business Card Catalog1\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCatalogBySubCategoryIdWithWebp",
    "title": "getCatalogBySubCategoryIdWithWebp",
    "name": "getCatalogBySubCategoryIdWithWebp",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":97, //compulsory\n\"page\":1, //compulsory\n\"item_count\":2 //compulsory\n\"is_free\":1 //optional, 1=free & 0=paid\n\"is_featured\":1 //optional, 1=featured(Ex: get template categories) & 0=normal(Ex: et sticker categories)\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalogs fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 21,\n\"is_next_page\": true,\n\"result\": [\n{\n\"catalog_id\": 498,\n\"name\": \"Party\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7914a7c12f5_catalog_img_1551439015.webp\",\n\"webp_original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7914a7c12f5_catalog_img_1551439015.webp\",\n\"is_featured\": 1,\n\"is_free\": 1,\n\"updated_at\": \"2019-03-01 11:16:56\"\n},\n{\n\"catalog_id\": 497,\n\"name\": \"Offer & Sales\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7912eee5c0e_catalog_img_1551438574.webp\",\n\"webp_original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7912eee5c0e_catalog_img_1551438574.webp\",\n\"is_featured\": 1,\n\"is_free\": 1,\n\"updated_at\": \"2019-03-01 11:09:35\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCatalogsByType",
    "title": "getCatalogsByType",
    "name": "getCatalogsByType",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":94, //compulsory\n\"is_free\":1 //optional, 1=free & 0=paid\n\"is_featured\":1 //optional, 1=featured & 0=normal\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalogs fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"catalog_id\": 279,\n\"name\": \"Animal1\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c39b8ba93df0_catalog_img_1547286714.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c39b8ba93df0_catalog_img_1547286714.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c39b8ba93df0_catalog_img_1547286714.png\",\n\"is_featured\": 0,\n\"is_free\": 1,\n\"updated_at\": \"2019-01-12 09:51:55\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCatalogsByTypeInWebp",
    "title": "getCatalogsByTypeInWebp",
    "name": "getCatalogsByTypeInWebp",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":94, //compulsory\n\"is_free\":1 //optional, 1=free & 0=paid\n\"is_featured\":1 //optional, 1=featured & 0=normal\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalogs fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"catalog_id\": 333,\n\"name\": \"Shonar Bangla\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c419ca410289_catalog_img_1547803812.webp\",\n\"is_featured\": 0,\n\"is_free\": 1,\n\"updated_at\": \"2019-01-18 09:30:12\"\n},\n{\n\"catalog_id\": 332,\n\"name\": \"Roboto\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c419c992fbeb_catalog_img_1547803801.webp\",\n\"is_featured\": 0,\n\"is_free\": 1,\n\"updated_at\": \"2019-01-18 09:30:01\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getContentByCatalogId",
    "title": "getContentByCatalogId",
    "name": "getContentByCatalogId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"catalog_id\":397, //compulsory\n\"page\":1, //compulsory\n\"item_count\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Content fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 135,\n\"is_next_page\": true,\n\"result\": [\n{\n\"img_id\": 3303,\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"is_featured\": \"\",\n\"is_free\": 1,\n\"is_portrait\": 0,\n\"search_category\": \"\"\n},\n{\n\"img_id\": 3304,\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"is_featured\": \"\",\n\"is_free\": 1,\n\"is_portrait\": 0,\n\"search_category\": \"\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCorruptedFontList",
    "title": "getCorruptedFontList",
    "name": "getCorruptedFontList",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"last_sync_time\":\"0\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Fonts details fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n  {\n  \"catalog_id\": 228,\n  \"name\": \"Roboto\",\n  \"is_removed\": 0,\n  \"is_free\": 0,\n  \"is_featured\": 0,\n  \"font_list\": [\n  {\n  \"font_id\": 33,\n  \"catalog_id\": 228,\n  \"font_name\": \"Roboto Thin\",\n  \"font_file\": \"Roboto-Thin.ttf\",\n  \"font_url\": \"http://192.168.0.115/videoflyer_backend/image_bucket/fonts/Roboto-Thin.ttf\",\n  \"ios_font_name\": \"Roboto-Thin\",\n  6800\"android_font_name\": \"fonts/nexa_rustsans_black.otf\"\n  }\n ]\n  }\n],\n\"last_sync_time\": \"2019-11-25 04:05:54\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getDeletedCatalogId",
    "title": "getDeletedCatalogId",
    "name": "getDeletedCatalogId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":81, //optional\n\"catalog_id_list\":[ //compulsory\n75,\n76\n]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Deleted catalog id fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"catalog_id_list\": [\n75,\n76\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getDeletedJsonId",
    "title": "getDeletedJsonId",
    "name": "getDeletedJsonId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"device_info\":\"\", //optional\n\"json_id_list\":[\n101,\n102,\n103,\n104,\n105,\n95,\n106\n]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Deleted json id fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"json_id_list\": [\n101,\n102,\n103,\n104,\n105,\n95\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getFeaturedCatalogBySubCategoryId",
    "title": "getFeaturedCatalogBySubCategoryId",
    "name": "getFeaturedCatalogBySubCategoryId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":10\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"catalog_id\": 32,\n\"name\": \"Frame-2022\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598affcc7cc80_catalog_img_1502281676.jpg\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598affcc7cc80_catalog_img_1502281676.jpg\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598affcc7cc80_catalog_img_1502281676.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getFeaturedJsonSampleData_webp",
    "title": "getFeaturedJsonSampleData_webp",
    "name": "getFeaturedJsonSampleData_webp",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":51\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All json fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"catalog_id\": 168,\n\"name\": \"Business Card Catalog2\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-08-11 04:13:20\",\n\"featured_cards\": [\n{\n\"json_id\": 414,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9747c534f_json_image_1512019783.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-08-31 10:02:15\"\n},\n{\n\"json_id\": 415,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f974dc5c1a_json_image_1512019789.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-08-31 10:02:03\"\n},\n{\n\"json_id\": 417,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f97592443d_json_image_1512019801.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-08-31 10:02:03\"\n},\n{\n\"json_id\": 418,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f975f6f461_json_image_1512019807.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-08-31 10:02:02\"\n},\n{\n\"json_id\": 419,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a1f9765255c2_json_image_1512019813.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2018-08-31 10:02:02\"\n}\n]\n},\n{\n\"catalog_id\": 167,\n\"name\": \"Business Card Catalog1\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a17fab520a09_catalog_img_1511520949.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a17fab520a09_catalog_img_1511520949.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a17fab520a09_catalog_img_1511520949.png\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2017-11-28 07:42:02\",\n\"featured_cards\": []\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getFeaturedSamplesWithCatalogs",
    "title": "getFeaturedSamplesWithCatalogs",
    "name": "getFeaturedSamplesWithCatalogs",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":97, //compulsory\n\"catalog_id\":0, //compulsory, pass 0 if you don't have catalog_id(for 1st API call)\n\"page\":1, //compulsory\n\"item_count\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All featured cards are fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 50,\n\"is_next_page\": true,\n\"category_list\": [\n{\n\"catalog_id\": 646,\n\"name\": \"Pinal\",\n\"is_featured\": 1,\n\"updated_at\": \"2019-07-17 08:17:49\"\n},\n{\n\"catalog_id\": 642,\n\"name\": \"Isha\",\n\"is_featured\": 1,\n\"updated_at\": \"2019-06-26 11:15:01\"\n}\n],\n\"sample_cards\": [\n{\n\"json_id\": 12057,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5d2edd42ddb44_json_image_1563352386.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 408,\n\"width\": 528,\n\"original_img_height\": 816,\n\"original_img_width\": 1056,\n\"updated_at\": \"2019-07-17 08:34:04\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getImageByUnsplash",
    "title": "getImageByUnsplash",
    "name": "getImageByUnsplash",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"search_query\":\"car\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Images fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total\": 96294,\n\"total_pages\": 3210,\n\"results\": [\n{\n\"id\": \"8qEuawM_txg\",\n\"created_at\": \"2018-10-22T06:00:30-04:00\",\n\"updated_at\": \"2018-12-23T11:00:34-05:00\",\n\"width\": 3000,\n\"height\": 4000,\n\"color\": \"#061A1A\",\n\"description\": null,\n\"urls\": {\n\"raw\": \"https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ\",\n\"full\": \"https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=85&fm=jpg&crop=entropy&cs=srgb&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ\",\n\"regular\": \"https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ\",\n\"small\": \"https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ\",\n\"thumb\": \"https://images.unsplash.com/photo-1540202403-b7abd6747a18?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=200&fit=max&ixid=eyJhcHBfaWQiOjQ3NDQ5fQ\"\n},\n\"links\": {\n\"self\": \"https://api.unsplash.com/photos/8qEuawM_txg\",\n\"html\": \"https://unsplash.com/photos/8qEuawM_txg\",\n\"download\": \"https://unsplash.com/photos/8qEuawM_txg/download\",\n\"download_location\": \"https://api.unsplash.com/photos/8qEuawM_txg/download\"\n},\n\"categories\": [],\n\"sponsored\": true,\n\"sponsored_by\": {\n\"id\": \"MEbW0Mv5SHk\",\n\"updated_at\": \"2018-10-22T14:47:15-04:00\",\n\"username\": \"maldives\",\n\"name\": \"Maldives Tourism\",\n\"first_name\": \"Maldives Tourism\",\n\"last_name\": null,\n\"twitter_username\": null,\n\"portfolio_url\": null,\n\"bio\": \"The sunny side of life.\",\n\"location\": \"Mal�, Maldives\",\n\"links\": {\n\"self\": \"https://api.unsplash.com/users/maldives\",\n\"html\": \"https://unsplash.com/@maldives\",\n\"photos\": \"https://api.unsplash.com/users/maldives/photos\",\n\"likes\": \"https://api.unsplash.com/users/maldives/likes\",\n\"portfolio\": \"https://api.unsplash.com/users/maldives/portfolio\",\n\"following\": \"https://api.unsplash.com/users/maldives/following\",\n\"followers\": \"https://api.unsplash.com/users/maldives/followers\"\n},\n\"profile_image\": {\n\"small\": \"https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=32&w=32\",\n\"medium\": \"https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=64&w=64\",\n\"large\": \"https://images.unsplash.com/profile-1540233904172-590b0facb2d0?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=128&w=128\"\n},\n\"instagram_username\": null,\n\"total_collections\": 0,\n\"total_likes\": 0,\n\"total_photos\": 0,\n\"accepted_tos\": false\n},\n\"sponsored_impressions_id\": \"3282145\",\n\"likes\": 501,\n\"liked_by_user\": false,\n\"current_user_collections\": [],\n\"slug\": null,\n\"user\": {\n\"id\": \"cYNNst8ZosY\",\n\"updated_at\": \"2018-12-24T17:54:48-05:00\",\n\"username\": \"seefromthesky\",\n\"name\": \"Ishan @seefromthesky\",\n\"first_name\": \"Ishan\",\n\"last_name\": \"@seefromthesky\",\n\"twitter_username\": \"SeefromtheSky\",\n\"portfolio_url\": \"http://www.instagram.com/seefromthesky\",\n\"bio\": \"??? ?????? ????? ?????? ????????? ??????? ??????\\r\\n ��� \\r\\nPeace and love. ?? #seefromthesky\\r\\n? ishan@seefromthesky.com\\r\\n\",\n\"location\": \"maldives\",\n\"links\": {\n\"self\": \"https://api.unsplash.com/users/seefromthesky\",\n\"html\": \"https://unsplash.com/@seefromthesky\",\n\"photos\": \"https://api.unsplash.com/users/seefromthesky/photos\",\n\"likes\": \"https://api.unsplash.com/users/seefromthesky/likes\",\n\"portfolio\": \"https://api.unsplash.com/users/seefromthesky/portfolio\",\n\"following\": \"https://api.unsplash.com/users/seefromthesky/following\",\n\"followers\": \"https://api.unsplash.com/users/seefromthesky/followers\"\n},\n\"profile_image\": {\n\"small\": \"https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=32&w=32\",\n\"medium\": \"https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=64&w=64\",\n\"large\": \"https://images.unsplash.com/profile-1470411901970-0f48a5d5e958?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&cs=tinysrgb&fit=crop&h=128&w=128\"\n},\n\"instagram_username\": \"seefromthesky\",\n\"total_collections\": 0,\n\"total_likes\": 64,\n\"total_photos\": 91,\n\"accepted_tos\": false\n},\n\"tags\": [\n{\n\"title\": \"underwater\"\n},\n{\n\"title\": \"reef\"\n},\n{\n\"title\": \"coral\"\n},\n{\n\"title\": \"water\"\n},\n{\n\"title\": \"maldives\"\n}\n],\n\"photo_tags\": [\n{\n\"title\": \"underwater\"\n},\n{\n\"title\": \"reef\"\n},\n{\n\"title\": \"coral\"\n},\n{\n\"title\": \"water\"\n},\n{\n\"title\": \"maldives\"\n}\n]\n}\n],\n\"is_next_page\": true,\n\"is_cache\": 0\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UnsplashController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getImagesByCatalogId",
    "title": "getImagesByCatalogId",
    "name": "getImagesByCatalogId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n \"catalog_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Images fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 3303,\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb53019f81_normal_image_1550562608.jpg\",\n\"is_json_data\": 0,\n\"json_data\": \"\",\n\"is_featured\": \"\",\n\"is_free\": 0,\n\"is_portrait\": 0,\n\"search_category\": \"\"\n},\n{\n\"img_id\": 3304,\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5c6bb530289e5_normal_image_1550562608.jpg\",\n\"is_json_data\": 0,\n\"json_data\": \"\",\n\"is_featured\": \"\",\n\"is_free\": 0,\n\"is_portrait\": 0,\n\"search_category\": \"\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getImagesFromPixabay",
    "title": "getImagesFromPixabay",
    "name": "getImagesFromPixabay",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"search_query\":\"nature\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Images fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_profile_url\": \"https://pixabay.com/users/\",\n\"is_next_page\": true,\n\"is_cache\": 0,\n\"result\": {\n\"totalHits\": 500,\n\"hits\": [\n{\n\"largeImageURL\": \"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_1280.jpg\",\n\"webformatHeight\": 373,\n\"webformatWidth\": 640,\n\"likes\": 1918,\n\"imageWidth\": 3160,\n\"id\": 1072823,\n\"user_id\": 1720744,\n\"views\": 546053,\n\"comments\": 218,\n\"pageURL\": \"https://pixabay.com/photos/road-forest-season-autumn-fall-1072823/\",\n\"imageHeight\": 1846,\n\"webformatURL\": \"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_640.jpg\",\n\"type\": \"photo\",\n\"previewHeight\": 87,\n\"tags\": \"road, forest, season\",\n\"downloads\": 219192,\n\"user\": \"valiunic\",\n\"favorites\": 1634,\n\"imageSize\": 3819762,\n\"previewWidth\": 150,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg\",\n\"previewURL\": \"https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg\"\n}\n],\n\"total\": 248144\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/PixabayController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getJsonData",
    "title": "getJsonData",
    "name": "getJsonData",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"json_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured json images fetch successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getJsonSampleData",
    "title": "getJsonSampleData",
    "name": "getJsonSampleData",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1,\n\"item_count\":10,\n\"catalog_id\":0,\n\"sub_category_id\":45\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 3,\n\"is_next_page\": false,\n\"data\": [\n{\n\"json_id\": 355,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 354,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f89953aa_catalog_image_1510834057.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 342,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a059c4cbadaa_catalog_image_1510317132.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getJsonSampleDataFilterBySearchTag",
    "title": "getJsonSampleDataFilterBySearchTag",
    "name": "getJsonSampleDataFilterBySearchTag",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":97, //compulsory\n\"search_category\":\"Leaderboard Ad\", //optional for templates screen\n\"page\":1, //optional for templates screen\n\"item_count\":20 //optional for templates screen\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Templates fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 11,\n\"is_next_page\": false,\n\"templates_with_categories\": [\n{\n\"category_name\": \"Logos\",\n\"content_list\": []\n},\n{\n\"category_name\": \"Business Cards\",\n\"content_list\": [\n{\n\"json_id\": 4768,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5cac6c693d405_json_image_1554803817.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 0,\n\"height\": 300,\n\"width\": 525,\n\"updated_at\": \"2019-04-10 08:09:41\"\n}\n]\n},\n{\n\"category_name\": \"Flyers\",\n\"content_list\": [\n{\n\"json_id\": 3390,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6f7f3e037d9_json_image_1550810942.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"height\": 400,\n\"width\": 325,\n\"updated_at\": \"2019-03-30 06:01:38\"\n}\n]\n}\n],\n\"template_list\": []\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getJsonSampleDataWithLastSyncTime",
    "title": "getJsonSampleDataWithLastSyncTime",
    "name": "getJsonSampleDataWithLastSyncTime",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":2,\n\"item_count\":10,\n\"catalog_id\":167,\n\"sub_category_id\":51,\n\"last_sync_time\": \"2017-11-28 00:00:00\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 18,\n\"is_next_page\": true,\n\"last_sync_time\": \"2017-11-28 06:42:11\",\n\"data\": [\n{\n\"json_id\": 407,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfe841fd30_json_image_1511849604.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 406,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfd7fadfc0_json_image_1511849343.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 405,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cfc994b4bd_json_image_1511849113.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 404,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cf9656d54c_json_image_1511848293.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 401,\n\"sample_image\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1cefcb29a2b_json_image_1511845835.jpg\",\n\"is_free\": 0,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"updated_at\": \"2018-06-21 12:04:36\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getJsonSampleDataWithLastSyncTime_webp",
    "title": "getJsonSampleDataWithLastSyncTime_webp",
    "name": "getJsonSampleDataWithLastSyncTime_webp",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory\n\"sub_category_id\": 97,\n\"catalog_id\": 398, //pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id\n\"page\": 1,\n\"item_count\": 2,\n\"last_sync_time\": \"2017-11-28 12:58:15\", //pass 0 on fist api call\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Samples fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 3,\n\"is_next_page\": false,\n\"data\": [\n{\n\"json_id\": 3326,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 0,\n\"height\": 256,\n\"width\": 512,\n\"search_category\": \"Twitter\",\n\"original_img_height\": 512,\n\"original_img_width\": 1024,\n\"updated_at\": \"2019-02-20 11:08:20\"\n},\n{\n\"json_id\": 3325,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d34be91f98_json_image_1550660798.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 408,\n\"width\": 528,\n\"search_category\": \"Brochure\",\n\"original_img_height\": 816,\n\"original_img_width\": 1056,\n\"updated_at\": \"2019-02-20 11:07:06\"\n}\n],\n\"last_sync_time\": \"2019-02-21 09:50:37\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getLink",
    "title": "getLink",
    "name": "getLink",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":2,\n\"platform\":\"Android\" //Or iOS\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 1,\n\"link_list\": [\n{\n\"advertise_link_id\": 51,\n\"name\": \"QR Scanner\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a043096329d3_banner_image_1510224022.jpg\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a043096329d3_banner_image_1510224022.jpg\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a043096329d3_banner_image_1510224022.jpg\",\n\"app_logo_thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/5a043096329d3_app_logo_image_1510224022.jpg\",\n\"app_logo_compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/5a043096329d3_app_logo_image_1510224022.jpg\",\n\"app_logo_original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/5a043096329d3_app_logo_image_1510224022.jpg\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en\",\n\"platform\": \"Android\",\n\"app_description\": \"This is test description.\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getLinkWithLastSyncTime",
    "title": "getLinkWithLastSyncTime",
    "name": "getLinkWithLastSyncTime",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\": 85,\n\"platform\": \"Android\",\n\"last_sync_time\": \"2017-11-28 00:00:00\",\n\"advertise_id_list\": [\n70,\n71,\n72,\n77,\n100,\n200\n]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"link_list\": [\n{\n\"advertise_link_id\": 77,\n\"name\": \"Romantic Love Photo Editor\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor\",\n\"platform\": \"Android\",\n\"app_description\": \"Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc.\",\n\"updated_at\": \"2018-06-25 10:55:05\"\n}\n],\n\"last_sync_time\": \"2018-06-25 10:55:05\",\n\"advertise_id_list\": [\n100\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getLinkWithoutToken",
    "title": "getLinkWithoutToken",
    "name": "getLinkWithoutToken",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":2,\n\"platform\":\"Android\" //Or iOS\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 14,\n\"link_list\": [\n{\n\"advertise_link_id\": 77,\n\"name\": \"Romantic Love Photo Editor\",\n\"thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e813f47368_banner_image_1511948607.png\",\n\"compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e813f47368_banner_image_1511948607.png\",\n\"original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e813f47368_banner_image_1511948607.png\",\n\"app_logo_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/thumbnail/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_compressed_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/compressed/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"app_logo_original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/original/5a1e814000aa9_app_logo_image_1511948608.png\",\n\"url\": \"https://play.google.com/store/apps/details?id=com.optimumbrewlab.lovephotoeditor\",\n\"platform\": \"Android\",\n\"app_description\": \"Romantic Love Photo Editor - Realistic Photo Effects, Beautiful Photo Frames, Stickers, etc.\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getSampleImagesForMobile",
    "title": "getSampleImagesForMobile",
    "name": "getSampleImagesForMobile",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":13\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Images Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 220,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33bf5cd88_original_img_1502360511.png\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33bf5cd88_original_img_1502360511.png\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33bf5cd88_original_img_1502360511.png\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c33c010ed8_display_img_1502360512.png\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c33c010ed8_display_img_1502360512.png\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c33c010ed8_display_img_1502360512.png\",\n\"image_type\": 1 // 1 = Background , 2 = Frame\n},\n{\n\"img_id\": 219,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c3141d844a_original_img_1502359873.png\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c3141d844a_original_img_1502359873.png\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c3141d844a_original_img_1502359873.png\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598c314294e53_display_img_1502359874.png\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598c314294e53_display_img_1502359874.png\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598c314294e53_display_img_1502359874.png\",\n\"image_type\": 1 // 1 = Background , 2 = Frame\n},\n{\n\"img_id\": 216,\n\"original_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e07757_original_img_1502345806.jpg\",\n\"original_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e07757_original_img_1502345806.jpg\",\n\"original_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e07757_original_img_1502345806.jpg\",\n\"display_thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/598bfa4e39443_display_img_1502345806.jpg\",\n\"display_compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/598bfa4e39443_display_img_1502345806.jpg\",\n\"display_original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/598bfa4e39443_display_img_1502345806.jpg\",\n\"image_type\": 1 // 1 = Background , 2 = Frame\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getTemplateWithCatalogs",
    "title": "getTemplateWithCatalogs",
    "name": "getTemplateWithCatalogs",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1, //compulsory\n\"catalog_id\":1, //compulsory, pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id\n\"is_get_data_for_1st_catalog\":1,//optional,pass 1 if you want sample images of 1st catalog\n\"page\":1, //compulsory\n\"item_count\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All templates are fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 128,\n\"is_next_page\": true,\n\"category_list\": [\n{\n\"catalog_id\": 168,\n\"name\": \"Business Card Catalog2\",\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1d0851d6d32_catalog_img_1511852113.png\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"catalog_id\": 259,\n\"name\": \"pavan\",\n\"thumbnail_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/thumbnail/5ce7a0ab5fbdc_catalog_img_1558683819.jpg\",\n\"compressed_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/compressed/5ce7a0ab5fbdc_catalog_img_1558683819.jpg\",\n\"original_img\": \"http://192.168.0.113/videoflyer_backend/image_bucket/original/5ce7a0ab5fbdc_catalog_img_1558683819.jpg\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"updated_at\": \"2019-06-17 03:33:07\"\n}\n],\n\"sample_cards\": [\n{\n\"json_id\": 3326,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 0,\n\"height\": 256,\n\"width\": 512,\n\"search_category\": \"Twitter\",\n\"original_img_height\": 512,\n\"original_img_width\": 1024,\n\"updated_at\": \"2019-02-20 11:08:20\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getTemplatesBySubCategoryTags",
    "title": "getTemplatesBySubCategoryTags",
    "name": "getTemplatesBySubCategoryTags",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":66, //compulsory\n\"category_name\":\"Business\", //optional on 1st API call for home screen\n\"page\":1, //compulsory\n\"item_count\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Templates fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 23,\n\"is_next_page\": true,\n\"category_list\": [\n{\n\"sub_category_tag_id\": 1,\n\"tag_name\": \"Business Banner\"\n}\n],\n\"template_list\": [\n{\n\"json_id\": 10669,\n\"sample_image\": \"http:\\/\\/192.168.0.113\\/photo_editor_lab_backend\\/image_bucket\\/webp_original\\/5d11bf76d377d_json_image_1561444214.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 1,\n\"height\": 400,\n\"width\": 325,\n\"search_category\": \"flyers,symbol,vector,illustration,label,design,desktop,discount,image,card,wholesale,vectors,christmas,sale,celebration,price,sign,decoration,banner,business,stock\",\n\"original_img_height\": 800,\n\"original_img_width\": 650,\n\"updated_at\": \"2019-06-25 06:40:08\",\n\"search_text\": 5.565430641174316\n},\n{\n\"json_id\": 10663,\n\"sample_image\": \"http:\\/\\/192.168.0.113\\/photo_editor_lab_backend\\/image_bucket\\/webp_original\\/5d11bebf04cca_json_image_1561444031.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 0,\n\"height\": 400,\n\"width\": 325,\n\"search_category\": \"flyers,no person,karaoke,retro,graphic design,music,isolated,classic,microphone,conceptual,bright,business,equipment,creativity,electronics,invention,achievement,illuminated,contemporary,glazed,rock\",\n\"original_img_height\": 800,\n\"original_img_width\": 650,\n\"updated_at\": \"2019-06-25 06:36:12\",\n\"search_text\": 1.8949640989303589\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getTemplatesWithLastSyncTime",
    "title": "getTemplatesWithLastSyncTime",
    "name": "getTemplatesWithLastSyncTime",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory\n\"sub_category_id\": 97,\n\"catalog_id\": 398, //pass 0 if you don't have catalog_id(in this case you get all featured cards) otherwise you have to pass specific catalog_id\n\"page\": 1,\n\"item_count\": 2,\n\"last_sync_time\": \"2017-11-28 09:50:37\" //pass 0 on fist api call\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Samples fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 3,\n\"is_next_page\": false,\n\"data\": [\n{\n\"json_id\": 3326,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d3523a43b5_json_image_1550660899.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 0,\n\"height\": 256,\n\"width\": 512,\n\"search_category\": \"Twitter\",\n\"original_img_height\": 512,\n\"original_img_width\": 1024,\n\"updated_at\": \"2019-02-20 11:08:20\"\n},\n{\n\"json_id\": 3325,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c6d34be91f98_json_image_1550660798.webp\",\n\"is_free\": 1,\n\"is_featured\": 1,\n\"is_portrait\": 0,\n\"height\": 408,\n\"width\": 528,\n\"search_category\": \"Brochure\",\n\"original_img_height\": 816,\n\"original_img_width\": 1056,\n\"updated_at\": \"2019-02-20 11:07:06\"\n}\n],\n\"last_sync_time\": \"2019-02-21 09:50:37\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "registerUserDeviceByDeviceUdid",
    "title": "registerUserDeviceByDeviceUdid",
    "name": "registerUserDeviceByDeviceUdid",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"sub_category_id\":1,\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_reg_id\": \"115a1a110\", //Mandatory\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en\",\n\"device_latitude\": \"\",\n\"device_library_version\": \"1\",\n\"device_local_code\": \"NA\",\n\"device_longitude\": \"\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\", //Mandatory\n\"device_registration_date\": \"2016-05-06T15:58:11 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"109111aa1121\", //Mandatory\n\"device_vendor_name\": \"Micromax\",\n\"project_package_name\": \"com.optimumbrew.projectsetup\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Device registered successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/RegisterController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "saveUserFeeds",
    "title": "saveUserFeeds",
    "name": "saveUserFeeds",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n Key: Authorization\n Value: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\nrequest_data:{\n\"sub_category_id\":45,\n\"json_id\":2146\n},\n\"file\":\"ob.png\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Image saved successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "searchCardsBySubCategoryId",
    "title": "searchCardsBySubCategoryId",
    "name": "searchCardsBySubCategoryId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory\n\"sub_category_id\":66,\n\"search_category\":\"Flyers\",\n\"page\":1,\n\"item_count\":2\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200, //return 427 when server not find any result related to your search_category\n\"message\": \"Templates fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"json_id\": 7355,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5b804d96_normal_image_1556796856.png\",\n\"is_free\": 0,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-08-29 11:06:37\",\n\"search_text\": 1.91667640209198\n},\n{\n\"json_id\": 7338,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5800ba64_normal_image_1556796800.png\",\n\"is_free\": 0,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-08-29 11:06:19\",\n\"search_text\": 1.91667640209198\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "searchCatalogByUser",
    "title": "searchCatalogByUser",
    "name": "searchCatalogByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory\n\"sub_category_id\":66,\n\"search_category\":\"grandient logo\",\n\"page\":1,\n\"item_count\":2\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200, //return 427 when server not find any result related to your search_category\n\"message\": \"Templates fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"code\": 200,\n\"message\": \"Catalogs fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 21,\n\"is_next_page\": true,\n\"result\": [\n{\n\"catalog_id\": 498,\n\"name\": \"Party\",\n\"webp_thumbnail_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5c7914a7c12f5_catalog_img_1551439015.webp\",\n\"webp_original_img\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5c7914a7c12f5_catalog_img_1551439015.webp\",\n\"is_featured\": 1,\n\"is_free\": 1,\n\"updated_at\": \"2019-03-01 11:16:56\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "searchNormalImagesBySubCategoryId",
    "title": "searchNormalImagesBySubCategoryId",
    "name": "searchNormalImagesBySubCategoryId",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{//all parameters are compulsory \n\"sub_category_id\":66,\n\"search_category\":\"india\",\n\"page\":1,\n\"item_count\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Content fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 6,\n\"is_next_page\": false,\n\"catalog_list\": [\n{\n\"catalog_id\": 606,\n\"name\": \"Flower\",\n\"thumbnail_img\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/thumbnail/5ccad4f1b8ee4_catalog_img_1556796657.png\",\n\"compressed_img\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad4f1b8ee4_catalog_img_1556796657.png\",\n\"original_img\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/original/5ccad4f1b8ee4_catalog_img_1556796657.png\",\n\"webp_thumbnail_img\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/webp_thumbnail/5ccad4f1b8ee4_catalog_img_1556796657.webp\",\n\"webp_original_img\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/webp_original/5ccad4f1b8ee4_catalog_img_1556796657.webp\",\n\"is_featured\": 0,\n\"is_free\": 1,\n\"updated_at\": \"2019-10-14 01:16:33\"\n}\n],\n\"content_list\": [\n{\n\"img_id\": 7281,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad50513133_normal_image_1556796677.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-14 01:30:59\",\n\"search_text\": 20.13739013671875\n},\n{\n\"img_id\": 7285,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5057288c_normal_image_1556796677.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-14 01:30:55\",\n\"search_text\": 20.13739013671875\n},\n{\n\"img_id\": 7283,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad50541763_normal_image_1556796677.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-14 01:30:54\",\n\"search_text\": 20.13739013671875\n},\n{\n\"img_id\": 7282,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5052b7d3_normal_image_1556796677.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-12 10:51:37\",\n\"search_text\": 20.13739013671875\n},\n{\n\"img_id\": 7284,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad5055c8fc_normal_image_1556796677.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-12 10:51:31\",\n\"search_text\": 20.13739013671875\n},\n{\n\"img_id\": 7280,\n\"sample_image\": \"http://192.168.0.115/photo_editor_lab_backend/image_bucket/compressed/5ccad504ec5c3_normal_image_1556796676.png\",\n\"is_free\": 1,\n\"is_featured\": null,\n\"is_portrait\": null,\n\"height\": 0,\n\"width\": 0,\n\"updated_at\": \"2019-10-12 07:04:36\",\n\"search_text\": 20.13739013671875\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "verifyPromoCode",
    "title": "verifyPromoCode",
    "name": "verifyPromoCode",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"promo_code\":\"123\", //compulsory\n\"package_name\":\"com.bg.invitationcardmaker\", //compulsory\n\"device_udid\":\"e9e24a9ce6ca5498\", //compulsory\n\"device_platform\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Promo code verified successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/http/controllers/UserController.php",
    "groupTitle": "User"
  }
] });

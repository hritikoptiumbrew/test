define({ "api": [
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
          "content": "{\n\"code\": 200,\n\"message\": \"category added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\"catalog_id\":1}\nfile[]:image.jpeg\nfile[]:image12.jpeg\nfile[]:image.png",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category images added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\nrequest_data:{\n\"is_replace\":0 //compulsory 0=do not replace the existing file, 2=replace the existing file\n},\nfile[]:1.jpg,\nfile[]:2.jpg,\nfile[]:3.jpg,\nfile[]:4.jpg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json images added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\"catalog_id\":1,\n\"image_type\":1},\noriginal_img:image1.jpeg,\ndisplay_img:image12.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured Background Images added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addFeaturedImage",
    "title": "addFeaturedImage",
    "name": "addFeaturedImage",
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
          "content": "request_data:{\n\"catalog_id\":10\n},\noriginal_img:image1.jpeg,\ndisplay_img:image12.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured Images added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\n\"catalog_id\": 155,\n\"is_free\": 1,\n\"is_featured\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n}\n},\nfile:image1.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Link Added Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addZipFile",
    "title": "addZipFile",
    "name": "addZipFile",
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
          "content": "{\nfile[]:1.jpg,\nfile[]:2.jpg,\nfile[]:3.jpg,\nfile[]:4.jpg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Json images added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n \"current_password\":\"**********\",\n\"new_password\":\"***********\"\n\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Password updated successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/LoginController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "createInvalidation",
    "title": "createInvalidation",
    "name": "createInvalidation",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"catalog_id\":3\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Deleted Successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"img_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Image Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"category deleted successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"sub_category_id\":3\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category deleted successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"email_id\":\"jitendra.uttamvastra@gmail.com\",\n\"password\":\"123456\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\",\n\"user_detail\": {\n\"id\": 1,\n\"user_name\": \"admin\",\n\"email_id\": \"admin@gmail.com\",\n\"social_uid\": null,\n\"signup_type\": null,\n\"profile_setup\": 0,\n\"is_active\": 1,\n\"create_time\": \"2017-05-05 09:57:26\",\n\"update_time\": \"2017-07-06 13:19:13\",\n\"attribute1\": null,\n\"attribute2\": null,\n\"attribute3\": null,\n\"attribute4\": null\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/LoginController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "doLogout",
    "title": "doLogout",
    "name": "doLogout",
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
          "content": "{\n\"code\": 200,\n\"message\": \"User have successfully logged out.\",\n\"cause\": \"\",\n\"data\": {\n\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/LoginController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Advertise Link Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"link_list\": [\n{\n\"advertise_link_id\": 44,\n\"name\": \"Suitescene\",\n\"platform\": \"iOS\",\n\"linked\": 1\n},\n{\n\"advertise_link_id\": 41,\n\"name\": \"Bhavesh Gabani\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 40,\n\"name\": \"Visa\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 39,\n\"name\": \"QR Code Scanner : Barcode QR-Code Generator App\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 38,\n\"name\": \"Photo Editor Lab � Stickers , Filters & Frames\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 37,\n\"name\": \"QR Barcode Scanner : QR Bar Code Generator App\",\n\"platform\": \"iOS\",\n\"linked\": 0\n},\n{\n\"advertise_link_id\": 36,\n\"name\": \"Cut Paste - Background Eraser\",\n\"platform\": \"iOS\",\n\"linked\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAllAdvertisementForLinkAdvertisement",
    "title": "getAllAdvertisementForLinkAdvertisement",
    "name": "getAllAdvertisementForLinkAdvertisement",
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
          "content": "{\n\"advertise_link_id\":57,\n\"category_id\":2\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"SubCategory Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"sub_category_id\": 33,\n\"name\": \"All Sticker Catalogs\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 47,\n\"name\": \"Collage maker Stickers\",\n\"linked\": 1\n},\n{\n\"sub_category_id\": 31,\n\"name\": \"Fancy QR Generator\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 36,\n\"name\": \"GreetingsCard Stickers\",\n\"linked\": 0\n},\n{\n\"sub_category_id\": 49,\n\"name\": \"Quotes Creator Stickers\",\n\"linked\": 1\n},\n{\n\"sub_category_id\": 28,\n\"name\": \"Selfie With Ganesha Stickers\",\n\"linked\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n \"page\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All Category Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 8,\n\"is_next_page\": false,\n\"category_list\": [\n{\n\"category_id\": 9,\n\"name\": \"demo 3\"\n},\n{\n\"category_id\": 8,\n\"name\": \"demo 2\"\n},\n{\n\"category_id\": 7,\n\"name\": \"demo1\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"category_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"SubCategory Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 0,\n\"category_list\": []\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getCatalogBySubCategoryId",
    "title": "getCatalogBySubCategoryId",
    "name": "getCatalogBySubCategoryId",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getImagesByCatalogIdForAdmin",
    "title": "getImagesByCatalogIdForAdmin",
    "name": "getImagesByCatalogIdForAdmin",
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
          "content": "{\n \"catalog_id\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Images Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 360,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a169952c71b0_catalog_image_1511430482.jpg\",\n\"is_json_data\": 0,\n\"json_data\": \"\",\n\"is_featured\": \"\",\n\"is_free\": 0\n},\n{\n\"img_id\": 359,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a1697482f0a2_json_image_1511429960.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a1697482f0a2_json_image_1511429960.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a1697482f0a2_json_image_1511429960.jpg\",\n\"is_json_data\": 1,\n\"json_data\": \"test\",\n\"is_featured\": \"0\",\n\"is_free\": 0\n},\n{\n\"img_id\": 352,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7f290a6df_catalog_image_1510833961.jpg\",\n\"is_json_data\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 440,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 210,\n\"width\": 210\n},\n{\n\"xPos\": 0,\n\"yPos\": 211,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 270,\n\"width\": 430\n},\n{\n\"xPos\": 353,\n\"yPos\": 439,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 320,\n\"width\": 297\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_1.6.png\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_1.6.jpg\",\n\"height\": 800,\n\"width\": 650,\n\"is_featured\": 0\n},\n\"is_featured\": \"0\",\n\"is_free\": 1\n},\n{\n\"img_id\": 355,\n\"thumbnail_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/thumbnail/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"compressed_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"original_img\": \"http://192.168.0.113/ob_photolab_backend/image_bucket/original/5a0d7faa3b1bc_catalog_image_1510834090.jpg\",\n\"is_json_data\": 1,\n\"json_data\": {\n\"text_json\": [],\n\"sticker_json\": [],\n\"image_sticker_json\": [\n{\n\"xPos\": 0,\n\"yPos\": 0,\n\"image_sticker_image\": \"\",\n\"angle\": 0,\n\"is_round\": 0,\n\"height\": 800,\n\"width\": 500\n}\n],\n\"frame_json\": {\n\"frame_image\": \"frame_15.7.png\"\n},\n\"background_json\": {},\n\"sample_image\": \"sample_15.7.jpg\",\n\"is_featured\": 0,\n\"height\": 800,\n\"width\": 800\n},\n\"is_featured\": \"1\",\n\"is_free\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getSubCategoryByCategoryId",
    "title": "getSubCategoryByCategoryId",
    "name": "getSubCategoryByCategoryId",
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
          "content": "{\n \"category_id\":1, //compulsory\n \"page\":1, //compulsory\n \"item_count\":100 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"SubCategory Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 2,\n\"is_next_page\": false,\n \"category_name\": \"Background\",\n\"category_list\": [\n{\n\"sub_category_id\": 10,\n\"category_id\": 1,\n\"name\": \"Love-3\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/5971dc9c891f5_category_img_1500634268.jpg\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/5971dc9c891f5_category_img_1500634268.jpg\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/5971dc9c891f5_category_img_1500634268.jpg\"\n},\n{\n\"sub_category_id\": 1,\n\"category_id\": 1,\n\"name\": \"Nature\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/59719cfa423f3_category_img_1500617978.jpg\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/59719cfa423f3_category_img_1500617978.jpg\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/59719cfa423f3_category_img_1500617978.jpg\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"getUserProfile Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_details\": [\n{\n\"id\": 1,\n\"first_name\": \"admin\",\n\"last_name\": \"admin\",\n\"phone_number_1\": \"9173527938\",\n\"profile_img\": \"http://localhost/bgchanger/image_bucket/thumbnail/595b4076a8c8c_profile_img_1499152502.jpg\",\n\"about_me\": \"i'm Admin.\",\n\"address_line_1\": \"Rander Road\",\n\"city\": \"surat\",\n\"state\": \"gujarat\",\n\"zip_code\": \"395010\",\n\"contry\": \"India\",\n\"latitude\": \"\",\n\"longitude\": \"\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/LoginController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"search category fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"category_id\": 1,\n\"name\": \"Featured\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"category_id\":1,\n\"name\":\"ca\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"SubCategory Search Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"category_list\": [\n{\n\"sub_category_id\": 28,\n\"name\": \"Sub-category\",\n\"thumbnail_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/thumbnail/597c6e5045aa8_category_img_1501326928.png\",\n\"compressed_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/compressed/597c6e5045aa8_category_img_1501326928.png\",\n\"original_img\": \"http://192.168.0.102/ob_photolab_backend/image_bucket/original/597c6e5045aa8_category_img_1501326928.png\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/NotificationController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\n\"catalog_id\":1,\n\"name\":\"bg-catalog\",\n\"is_free\":1,\n\"is_featured\":1\n}\nfile:image.png //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Updated Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"category updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\"img_id\":1,\n\"image_type\":1},\noriginal_img:image1.jpeg,\ndisplay_img:image12.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured Background Images updated successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateFeaturedImage",
    "title": "updateFeaturedImage",
    "name": "updateFeaturedImage",
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
          "content": "request_data:{\n\"img_id\":10\n},\noriginal_img:image1.jpeg,\ndisplay_img:image12.jpeg\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Featured Images added successfully!.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Link Updated Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\n\"sub_category_id\":2,\n\"name\":\"Love-Category\"\n}\nfile:image.png //optional",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"sub category updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "request_data:{\n\"img_id\":1\n}\nfile:\"\"",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Image Updated Successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Profile Updated Successfully.\",\n\"cause\": \"\",\n\"response\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./documentation/main.js",
    "group": "C__wamp64_www_photo_editor_lab_backend_api_documentation_main_js",
    "groupTitle": "C__wamp64_www_photo_editor_lab_backend_api_documentation_main_js",
    "name": ""
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
    "filename": "./app/Http/Controllers/SubscriptionPaymentController.php",
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
          "content": "{\n\"order_info\": {\n\"sub_category_id\": 1,\n\"purchase_token\": \"\",\n\"auto_renewing\": \"false\",\n\"order_id\": \"1000000297266758\",\n\"product_id\": \"qrcodescanner_pro\",\n\"tot_order_amount\": 1.99,\n\"package_name\": \"com.optimumbrewlab.bgchanger-\",\n\"purchase_time\": \"1494339022669\",\n\"purchase_state\": \"1\",\n\"receipt_base64_data\": \"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\"\n},\n\"device_info\": {\n\"sub_category_id\": 1,\n\"device_udid\": \"8DC1456E-3EBC-43FA-985A-DDAC492418FD\",\n\"device_reg_id\": \"911F5C2F54F94DC6037C1A249B0BC98BFF5B17DF2A1C8CE5A546201A5B8DCF6B\",\n\"device_local_code\": \"en-US\",\n\"device_platform\": \"ios\",\n\"device_resolution\": \"320.0568.0\",\n\"device_library_version\": \"\",\n\"device_carrier\": \"\",\n\"device_os_version\": \"10.0.2\",\n\"device_default_time_zone\": \"Asia/Kolkata\",\n\"device_type\": \"phone\",\n\"project_package_name\": \"com.optimumbrewlab.bgchanger-\",\n\"device_vendor_name\": \"Apple\",\n\"device_model_name\": \"iPhone\",\n\"device_longitude\": \"0.0\",\n\"device_latitude\": \"0.0\",\n\"device_country_code\": \"US\",\n\"device_application_version\": \"1\",\n\"device_language\": \"en-US\",\n\"device_registration_date\": \"2017-05-09 14:10:22 +0000\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Payment was successful.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/SubscriptionPaymentController.php",
    "groupTitle": "Payment_Subscription"
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
    "filename": "./app/Http/Controllers/NotificationController.php",
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
    "filename": "./app/Http/Controllers/LoginController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getCatalogBySubCategoryId",
    "title": "getCatalogBySubCategoryId",
    "name": "getCatalogBySubCategoryId",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 5,\n\"category_name\": \"Independence Day Stickers\",\n\"category_list\": [\n{\n\"catalog_id\": 84,\n\"name\": \"Misc\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d551036b09_catalog_img_1502434576.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d551036b09_catalog_img_1502434576.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d551036b09_catalog_img_1502434576.png\",\n\"is_free\": 0,\n\"is_featured\": 1\n},\n{\n\"catalog_id\": 80,\n\"name\": \"Circle\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64d7c306f_catalog_img_1502438615.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64d7c306f_catalog_img_1502438615.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64d7c306f_catalog_img_1502438615.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 81,\n\"name\": \"Flag\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64c7af06f_catalog_img_1502438599.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64c7af06f_catalog_img_1502438599.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64c7af06f_catalog_img_1502438599.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 82,\n\"name\": \"Map\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d64afc90f8_catalog_img_1502438575.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d64afc90f8_catalog_img_1502438575.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d64afc90f8_catalog_img_1502438575.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n},\n{\n\"catalog_id\": 83,\n\"name\": \"Text\",\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d649f4442e_catalog_img_1502438559.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d649f4442e_catalog_img_1502438559.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d649f4442e_catalog_img_1502438559.png\",\n\"is_free\": 0,\n\"is_featured\": 0\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"code\": 200,\n\"message\": \"Catalog Images Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"image_list\": [\n{\n\"img_id\": 13,\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e5ec3f2_catalog_image_1502433765.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e5ec3f2_catalog_image_1502433765.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d51e5ec3f2_catalog_image_1502433765.png\"\n},\n{\n\"img_id\": 14,\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e65fdf3_catalog_image_1502433766.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e65fdf3_catalog_image_1502433766.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d51e65fdf3_catalog_image_1502433766.png\"\n},\n{\n\"img_id\": 11,\n\"thumbnail_img\": \"http://localhost/ob_photolab_backend/image_bucket/thumbnail/598d51e4e7d68_catalog_image_1502433764.png\",\n\"compressed_img\": \"http://localhost/ob_photolab_backend/image_bucket/compressed/598d51e4e7d68_catalog_image_1502433764.png\",\n\"original_img\": \"http://localhost/ob_photolab_backend/image_bucket/original/598d51e4e7d68_catalog_image_1502433764.png\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdminController.php",
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
          "content": "{\n\"category\":\"flower\",\n\"page\":1,\n\"item_count\":3\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Images are fatched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"is_cache\": 0,\n\"result\": {\n\"totalHits\": 500,\n\"hits\": [\n{\n\"largeImageURL\": \"https://pixabay.com/get/e831b30f2afc1c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg\",\n\"webformatHeight\": 640,\n\"webformatWidth\": 425,\n\"likes\": 182,\n\"imageWidth\": 2136,\n\"id\": 142028,\n\"user_id\": 41549,\n\"views\": 45888,\n\"comments\": 15,\n\"pageURL\": \"https://pixabay.com/en/lotus-pink-lotus-flower-plant-flowers-lo-142028/\",\n\"imageHeight\": 3216,\n\"webformatURL\": \"https://pixabay.com/get/e831b30f2afc1c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg\",\n\"type\": \"photo\",\n\"previewHeight\": 150,\n\"tags\": \"lotus pink lotus flower plant flowers lotus flower flower flower flowers flowers flowers flowers flowers\",\n\"downloads\": 12277,\n\"user\": \"artzhangqingfeng\",\n\"favorites\": 173,\n\"imageSize\": 978322,\n\"previewWidth\": 100,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2017/08/17/18-37-52-458_250x250.jpg\",\n\"previewURL\": \"https://cdn.pixabay.com/photo/2013/06/29/06/24/lotus-142028_150.jpg\"\n},\n{\n\"largeImageURL\": \"https://pixabay.com/get/ef30b9082df51c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg\",\n\"webformatHeight\": 640,\n\"webformatWidth\": 612,\n\"likes\": 164,\n\"imageWidth\": 2200,\n\"id\": 658751,\n\"user_id\": 784204,\n\"views\": 37932,\n\"comments\": 20,\n\"pageURL\": \"https://pixabay.com/en/bells-flower-flowers-blue-flower-black-n-658751/\",\n\"imageHeight\": 2300,\n\"webformatURL\": \"https://pixabay.com/get/ef30b9082df51c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg\",\n\"type\": \"photo\",\n\"previewHeight\": 150,\n\"tags\": \"bells flower flowers blue flower black nature spring blue purple flower blue flower background flowers flowers flowers flowers flowers blue flower flower\",\n\"downloads\": 18587,\n\"user\": \"Catharina77\",\n\"favorites\": 197,\n\"imageSize\": 294501,\n\"previewWidth\": 144,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2015/11/27/12-31-26-612_250x250.jpg\",\n\"previewURL\": \"https://cdn.pixabay.com/photo/2015/03/04/12/59/bells-flower-658751_150.jpg\"\n},\n{\n\"largeImageURL\": \"https://pixabay.com/get/ea37b1072ff01c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_1280.jpg\",\n\"webformatHeight\": 640,\n\"webformatWidth\": 437,\n\"likes\": 159,\n\"imageWidth\": 1365,\n\"id\": 320874,\n\"user_id\": 217857,\n\"views\": 38553,\n\"comments\": 19,\n\"pageURL\": \"https://pixabay.com/en/tulip-flower-bloom-pink-flowers-spring-n-320874/\",\n\"imageHeight\": 2000,\n\"webformatURL\": \"https://pixabay.com/get/ea37b1072ff01c22d2524518b7454590e570e5d204b014439cf9c87caee8b6_640.jpg\",\n\"type\": \"photo\",\n\"previewHeight\": 150,\n\"tags\": \"tulip flower bloom pink flowers spring nature tulip tulip tulip flower flower flower flowers flowers flowers flowers flowers spring spring nature\",\n\"downloads\": 13673,\n\"user\": \"Anelka\",\n\"favorites\": 159,\n\"imageSize\": 420525,\n\"previewWidth\": 102,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2014/04/10/14-20-41-498_250x250.jpg\",\n\"previewURL\": \"https://cdn.pixabay.com/photo/2014/04/10/11/27/tulip-320874_150.jpg\"\n}\n],\n\"total\": 16885\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/PixabayController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"page\":2,\n\"item_count\":10,\n\"catalog_id\":167,\n\"sub_category_id\":51,\n\"last_sync_time\": \"2017-11-28 00:00:00\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All json fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 23,\n\"is_next_page\": true,\n\"last_sync_time\": \"2018-06-21 12:04:36\",\n\"data\": [\n{\n\"json_id\": 1190,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5ab0ae54e2105_json_image_1521528404.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:36\"\n},\n{\n\"json_id\": 1185,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_original/5ab0af12d9bae_json_image_1521528594.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 1,\n\"updated_at\": \"2018-06-21 12:04:32\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/AdminController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getVideosFromPixabay",
    "title": "getVideosFromPixabay",
    "name": "getVideosFromPixabay",
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
          "content": "{\n\"category\":\"water\",\n\"page\":1,\n\"item_count\":3\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"videos are fatched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"is_cache\": 0,\n\"result\": {\n\"totalHits\": 500,\n\"hits\": [\n{\n\"picture_id\": \"529921736\",\n\"videos\": {\n\"large\": {\n\"url\": \"\",\n\"width\": 0,\n\"size\": 0,\n\"height\": 0\n},\n\"small\": {\n\"url\": \"https://player.vimeo.com/external/135733055.sd.mp4?s=4755d2adc868862c3e5bb601a4841c32debe22c8&profile_id=112\",\n\"width\": 640,\n\"size\": 3709798,\n\"height\": 310\n},\n\"medium\": {\n\"url\": \"https://player.vimeo.com/external/135733055.hd.mp4?s=d8ad3de28c7bda1746059d926a2cde7d4198348c&profile_id=113\",\n\"width\": 1280,\n\"size\": 11624131,\n\"height\": 620\n},\n\"tiny\": {\n\"url\": \"https://player.vimeo.com/external/135733055.mobile.mp4?s=326829edd29f45b58f45cfaaa1107d13ecb17eb6&profile_id=116\",\n\"width\": 480,\n\"size\": 1747635,\n\"height\": 232\n}\n},\n\"tags\": \"rain, thunder, water\",\n\"downloads\": 34362,\n\"likes\": 256,\n\"favorites\": 204,\n\"duration\": 36,\n\"id\": 78,\n\"user_id\": 1280814,\n\"views\": 92826,\n\"comments\": 41,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2015/08/07/22-32-32-276_250x250.jpg\",\n\"pageURL\": \"https://pixabay.com/videos/id-78/\",\n\"type\": \"film\",\n\"user\": \"DistillVideos\"\n},\n{\n\"picture_id\": \"583481279\",\n\"videos\": {\n\"large\": {\n\"url\": \"https://player.vimeo.com/external/176282263.hd.mp4?s=5ae9c441e89ee36646286c22fddc6c8781946c7d&profile_id=169\",\n\"width\": 1920,\n\"size\": 32514692,\n\"height\": 1080\n},\n\"small\": {\n\"url\": \"https://player.vimeo.com/external/176282263.sd.mp4?s=eae20877d2f66cd5b7481c8e9ac2b4b10fd92bef&profile_id=165\",\n\"width\": 960,\n\"size\": 7392540,\n\"height\": 540\n},\n\"medium\": {\n\"url\": \"https://player.vimeo.com/external/176282263.hd.mp4?s=5ae9c441e89ee36646286c22fddc6c8781946c7d&profile_id=174\",\n\"width\": 1280,\n\"size\": 12518329,\n\"height\": 720\n},\n\"tiny\": {\n\"url\": \"https://player.vimeo.com/external/176282263.sd.mp4?s=eae20877d2f66cd5b7481c8e9ac2b4b10fd92bef&profile_id=164\",\n\"width\": 640,\n\"size\": 2470905,\n\"height\": 360\n}\n},\n\"tags\": \"sea, wave, golden\",\n\"downloads\": 54973,\n\"likes\": 341,\n\"favorites\": 291,\n\"duration\": 40,\n\"id\": 4006,\n\"user_id\": 1024927,\n\"views\": 108996,\n\"comments\": 64,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2017/10/03/16-01-13-529_250x250.png\",\n\"pageURL\": \"https://pixabay.com/videos/id-4006/\",\n\"type\": \"film\",\n\"user\": \"outlinez\"\n},\n{\n\"picture_id\": \"540200545\",\n\"videos\": {\n\"large\": {\n\"url\": \"https://player.vimeo.com/external/142801793.hd.mp4?s=7fb230aa374b14694792fc7ab23e31ca40cd4117&profile_id=119\",\n\"width\": 1920,\n\"size\": 8542568,\n\"height\": 1080\n},\n\"small\": {\n\"url\": \"https://player.vimeo.com/external/142801793.sd.mp4?s=452bc0b5bb4ddc978189a41e1eacdc3409edb7e8&profile_id=112\",\n\"width\": 640,\n\"size\": 1448790,\n\"height\": 360\n},\n\"medium\": {\n\"url\": \"https://player.vimeo.com/external/142801793.hd.mp4?s=7fb230aa374b14694792fc7ab23e31ca40cd4117&profile_id=113\",\n\"width\": 1280,\n\"size\": 4826988,\n\"height\": 720\n},\n\"tiny\": {\n\"url\": \"https://player.vimeo.com/external/142801793.mobile.mp4?s=b7f949c7dab73b2c48da20ee73d10c48a6f68a18&profile_id=116\",\n\"width\": 480,\n\"size\": 579502,\n\"height\": 270\n}\n},\n\"tags\": \"bubbles, air, underwater\",\n\"downloads\": 33843,\n\"likes\": 219,\n\"favorites\": 231,\n\"duration\": 15,\n\"id\": 1085,\n\"user_id\": 1283884,\n\"views\": 64453,\n\"comments\": 29,\n\"userImageURL\": \"https://cdn.pixabay.com/user/2015/08/09/12-33-44-788_250x250.png\",\n\"pageURL\": \"https://pixabay.com/videos/id-1085/\",\n\"type\": \"film\",\n\"user\": \"Vimeo-Free-Videos\"\n}\n],\n\"total\": 1159\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/PixabayController.php",
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
    "filename": "./app/Http/Controllers/RegisterController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
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
          "content": "{\n\"sub_category_id\":56,//compulsory\n\"search_category\":\"Wedd\",//compulsory\n\"page\":1,//compulsory\n\"item_count\":10//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200, //return 427 when server not find any result related to your search_category\n\"message\": \"Templates fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 4,\n\"is_next_page\": false,\n\"result\": [\n{\n\"json_id\": 470,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a30ceb599c91_json_image_1513148085.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 1,\n\"height\": 400,\n\"width\": 325,\n\"updated_at\": \"2018-10-02 11:29:29\"\n},\n{\n\"json_id\": 463,\n\"sample_image\": \"http://192.168.0.113/photo_editor_lab_backend/image_bucket/webp_thumbnail/5a30cbb7d3d62_json_image_1513147319.webp\",\n\"is_free\": 1,\n\"is_featured\": 0,\n\"is_portrait\": 1,\n\"height\": 400,\n\"width\": 325,\n\"updated_at\": \"2018-10-02 11:28:40\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UserController.php",
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
    "filename": "./app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  }
] });

define({ "api": [
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://www.jdr-delain.net/api/v2/auth/"
      }
    ],
    "type": "delete",
    "url": "/auth/",
    "title": "Deletes an existing token",
    "name": "deleteToken",
    "group": "Auth",
    "description": "<p>Supprime le token</p>",
    "header": {
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"X-delain-auth\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "NoToken",
            "description": "<p>Token non transmis</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNotFound",
            "description": "<p>Token non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "AccountNotFound",
            "description": "<p>Compte non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNonUUID",
            "description": "<p>Le token n'est pas un UUID</p>"
          }
        ]
      }
    },
    "filename": "./auth.php",
    "groupTitle": "Auth"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://jdr-delain.net/api/v2/auth"
      }
    ],
    "type": "post",
    "url": "/auth/",
    "title": "Request a new token",
    "name": "requestToken",
    "group": "Auth",
    "description": "<p>Permet de demander un token d'identification qu'il faudra faire suivre pour les prochaines demandes</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "login",
            "description": "<p>Login du compte</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Password du compte</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n  \"login\": \"monlogin\",\n  \"password\": \"monpassword\"\n}",
          "type": "json"
        }
      ]
    },
    "header": {
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"Content-type\": \"application/json\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "FailedAuth",
            "description": "<p>Authentification échouée</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "compte",
            "description": "<p>Numéro du compte</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token à garder</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"compte\": \"2\",\n  \"token\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./auth.php",
    "groupTitle": "Auth"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://jdr-delain.net/api/v2/compte"
      }
    ],
    "type": "get",
    "url": "/compte/",
    "title": "retourne les détails du compte",
    "name": "CompteDetail",
    "group": "Compte",
    "description": "<p>Permet de demander le détail du compte</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "X-delain-auth",
            "description": "<p>Token</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"X-delain-auth\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "NoToken",
            "description": "<p>Token non transmis</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNotFound",
            "description": "<p>Token non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "AccountNotFound",
            "description": "<p>Compte non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNonUUID",
            "description": "<p>Le token n'est pas un UUID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "json",
            "optional": false,
            "field": "Tableau",
            "description": "<p>des données</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"compte\": \"2\",\n  \"token\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./compte.php",
    "groupTitle": "Compte"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://jdr-delain.net/api/v2/perso"
      }
    ],
    "type": "get",
    "url": "/compte/persos",
    "title": "Liste les persos d'un compte",
    "name": "ComptePersos",
    "group": "Compte",
    "description": "<p>Permet de lister les personnages d'un compte</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "X-delain-auth",
            "description": "<p>Token</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"X-delain-auth\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "NoToken",
            "description": "<p>Token non transmis</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNotFound",
            "description": "<p>Token non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "AccountNotFound",
            "description": "<p>Compte non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNonUUID",
            "description": "<p>Le token n'est pas un UUID</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "PersoExists",
            "description": "<p>Il existe déjà un perso avec ce nom</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "NotInteger",
            "description": "<p>Valeur non entière</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": true,
            "field": "horsPersos",
            "defaultValue": "false",
            "description": "<p>Si à true, on n'affiche pas les persos (que les fams)</p>"
          },
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": true,
            "field": "horsFam",
            "defaultValue": "false",
            "description": "<p>Si à true, on n'affiche pas les fams (que les persos)</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "json",
            "optional": false,
            "field": "Tableau",
            "description": "<p>des données</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\"persos\":[{\"perso_cod\":1},{\"perso_cod\":3}]}",
          "type": "json"
        }
      ]
    },
    "filename": "./compte_persos.php",
    "groupTitle": "Compte"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://jdr-delain.net/api/v2/news"
      }
    ],
    "type": "get",
    "url": "/news/",
    "title": "Retourne les news",
    "name": "news",
    "group": "News",
    "description": "<p>Permet de demander les news (par 5)</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "start_news",
            "defaultValue": "0",
            "description": "<p>Numéro de la première news demandée pour pagination</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "json",
            "optional": false,
            "field": "Tableau",
            "description": "<p>des données</p>"
          }
        ]
      }
    },
    "filename": "./news.php",
    "groupTitle": "News"
  },
  {
    "version": "2.0.0",
    "sampleRequest": [
      {
        "url": "https://jdr-delain.net/api/v2/perso"
      }
    ],
    "type": "post",
    "url": "/perso",
    "title": "Crée un nouveau perso",
    "name": "CreePerso",
    "group": "Perso",
    "description": "<p>Permet de créer un nouveau perso</p>",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "string",
            "optional": false,
            "field": "X-delain-auth",
            "description": "<p>Token</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"X-delain-auth\": \"d5f60c54-2aac-4074-b2bb-cbedebb396b8\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "403": [
          {
            "group": "403",
            "optional": false,
            "field": "NoToken",
            "description": "<p>Token non transmis</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNotFound",
            "description": "<p>Token non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "AccountNotFound",
            "description": "<p>Compte non trouvé dans la base</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "TokenNonUUID",
            "description": "<p>Le token n'est pas un UUID</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "PersoExists",
            "description": "<p>Il existe déjà un perso avec ce nom</p>"
          },
          {
            "group": "403",
            "optional": false,
            "field": "NotInteger",
            "description": "<p>Valeur non entière</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nom",
            "description": "<p>Nom du perso</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "force",
            "description": "<p>Force</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "con",
            "description": "<p>Constitution</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "dex",
            "description": "<p>Dextérité</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "optional": false,
            "field": "intel",
            "description": "<p>Intelligence</p>"
          },
          {
            "group": "Parameter",
            "type": "Integer",
            "allowedValues": [
              "1",
              "2",
              "3"
            ],
            "optional": false,
            "field": "race",
            "description": "<p>Code race</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"guerrier\"",
              "\"bucheron\"",
              "\"monk\"",
              "\"mage\"",
              "\"explo\"",
              "\"mineur\"",
              "\"archer\""
            ],
            "optional": false,
            "field": "voie",
            "description": "<p>La voie choisie (Hormandre ou SalMorv)</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "allowedValues": [
              "\"H\"",
              "\"S\""
            ],
            "optional": false,
            "field": "poste",
            "description": "<p>Poste d'entrée (Hormandre ou SalMorv)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n  \"nom\": \"monperso\",\n  \"force\": 12,\n  \"con\": 12,\n  \"dex\": 12,\n  \"intel\": 9,\n  \"voie\": \"guerrier\",\n  \"poste\": \"H\",\n   \"race\": 1\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "json",
            "optional": false,
            "field": "Tableau",
            "description": "<p>des données</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"perso\": \"2\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./perso.php",
    "groupTitle": "Perso"
  }
] });

<?php

return [
    'topic' => [
        'name' => 'Yazılım',
        'slug' => 'software',
        'description' => 'Sıfırdan ileri seviye yazılım mimarlığına giden serüven. Kodların gücünü keşfet!',
    ],
    'titles' => [
        ['level' => 1, 'name' => 'Syntax Parçası', 'icon' => ';', 'min_xp' => 0],
        ['level' => 2, 'name' => 'Değişken', 'icon' => 'x=', 'min_xp' => 100],
        ['level' => 3, 'name' => 'Koşullu İfade', 'icon' => 'if', 'min_xp' => 250],
        ['level' => 4, 'name' => 'Döngü', 'icon' => '↻', 'min_xp' => 450],
        ['level' => 5, 'name' => 'Fonksiyon', 'icon' => 'ƒ()', 'min_xp' => 700],
        ['level' => 6, 'name' => 'Yazılım Mimarı', 'icon' => '◆', 'min_xp' => 1000],
    ],
    'regions' => [
        [
            'name' => 'Değişkenler Vadisi',
            'slug' => 'variables-valley',
            'icon' => 'x=',
            'x' => 12, 'y' => 85,
            'description' => 'Maceran başlıyor! Bilgisayarın hafızasında verileri nasıl tutacağını ve onları nasıl isimlendireceğini burada öğreneceksin.',
            'lessons' => [
                ['order' => 1, 'title' => 'Değişken Nedir?', 'body' => 'Değişkenler, verileri sakladığımız kutulardır. JavaScript\'te en modern kutu oluşturma yöntemi `let` anahtar kelimesidir. Örneğin: `let isim = "Ahmet";`'],
                ['order' => 2, 'title' => 'Sabitler (const)', 'body' => 'İçindeki değeri bir daha asla değiştirmeyeceğin kutular için `const` kullanılır. Güvenlidir ve hataları önler. `const pi = 3.14;`'],
                ['order' => 3, 'title' => 'Veri Tipleri (Metinler)', 'body' => 'Metinsel (String) veriler her zaman tırnak işareti içinde yazılır. İki metni `+` ile birleştirebilirsin. `let tamAd = "Ali" + " Yılmaz";`'],
                ['order' => 4, 'title' => 'Veri Tipleri (Sayılar)', 'body' => 'Sayısal (Number) veriler tırnaksız yazılır. Matematiksel işlemler doğrudan yapılabilir. `let sonuc = 10 * 5;`'],
            ],
            'questions' => [
                ['body' => 'JavaScript\'te değeri sonradan değiştirilebilen bir değişken oluşturmak için hangi anahtar kelime kullanılır?', 'options' => ['const', 'var', 'let', 'int'], 'correct_index' => 2, 'explanation' => 'Modern JavaScript\'te değeri değişebilen değişkenler let ile tanımlanır.', 'difficulty' => 'easy'],
                ['body' => '`const yas = 20; yas = 21;` kodunun sonucu ne olur?', 'options' => ['yas değişkeni 21 olur', 'Hata verir', 'yas değişkeni undefined olur', 'Hiçbir şey olmaz'], 'correct_index' => 1, 'explanation' => 'const ile tanımlanan sabitlerin değeri sonradan değiştirilemez, TypeError fırlatır.', 'difficulty' => 'medium'],
                ['body' => 'Metinsel verileri (String) tanımlarken hangisi KULLANILMAZ?', 'options' => ['Tek tırnak (\')', 'Çift tırnak (")', 'Backtick (`)', 'Köşeli parantez ([])'], 'correct_index' => 3, 'explanation' => 'Köşeli parantezler dizileri (array) tanımlamak için kullanılır, metinler için değil.', 'difficulty' => 'easy'],
                ['body' => '`let x = "5" + 2;` işleminden sonra x\'in değeri ne olur?', 'options' => ['7', '52', 'NaN', 'Hata'], 'correct_index' => 1, 'explanation' => 'Bir metin (String) ile bir sayı toplandığında, JavaScript sayıyı metne çevirir ve yan yana birleştirir.', 'difficulty' => 'hard'],
                ['body' => 'Hangisi geçerli bir değişken isimlendirmesidir?', 'options' => ['1inciKullanici', 'benim-Adim', 'kullaniciYasi', 'let'], 'correct_index' => 2, 'explanation' => 'Değişken isimleri rakamla başlayamaz, tire (-) içeremez ve ayrılmış kelimeler (let gibi) olamaz.', 'difficulty' => 'medium'],
            ],
            'coding_tasks' => [
                [
                    'title' => 'İlk Fonksiyonun: Toplama',
                    'description' => 'Parametre olarak verilen iki sayıyı toplayan `topla` isimli bir fonksiyon yaz.',
                    'starter_code' => "function topla(a, b) {\n  // Kodunu buraya yaz\n  \n}",
                    'hint' => 'return a + b; kullanarak sonucu geri döndürebilirsin.',
                    'test_cases' => [
                        'function' => 'topla',
                        'cases' => [
                            ['args' => [2, 3], 'expected' => 5],
                            ['args' => [0, 0], 'expected' => 0],
                            ['args' => [-5, 10], 'expected' => 5],
                            ['args' => [-2, -2], 'expected' => -4]
                        ]
                    ]
                ]
            ]
        ],
        [
            'name' => 'Koşullar Kavşağı',
            'slug' => 'conditions-crossroads',
            'icon' => 'if',
            'x' => 30, 'y' => 72,
            'description' => 'Yol ikiye ayrılıyor. Programına karar vermeyi, doğru ve yanlış (boolean) kavramlarını kullanarak mantıksal yollar çizmeyi öğreteceksin.',
            'lessons' => [
                ['order' => 1, 'title' => 'Karar Verme (if)', 'body' => 'Eğer bir şart doğruysa (true) bir kod bloğunu çalıştırmak için `if` kullanılır. `if (yas >= 18) { console.log("Girebilirsin"); }`'],
                ['order' => 2, 'title' => 'Değilse (else)', 'body' => 'Şart sağlanmadığında başka bir işlem yapmak için `else` kullanılır. `if (yagmurVar) { şemsiyeAl(); } else { güneşGözlüğüAl(); }`'],
                ['order' => 3, 'title' => 'Eşitlik Kontrolü', 'body' => 'İki değerin eşit olup olmadığını kontrol etmek için `===` kullanılır. Bu hem değeri hem de tipi kontrol eder. `5 === "5"` sonucu `false` çıkar.'],
                ['order' => 4, 'title' => 'Mantıksal Operatörler', 'body' => 'Birden fazla şartı birleştirmek için VE (`&&`) veya VEYA (`||`) kullanılır. `if (yas >= 18 && ehliyetVar)`'],
            ],
            'questions' => [
                ['body' => '`if` bloğunun çalışması için parantez içindeki ifadenin sonucu ne olmalıdır?', 'options' => ['false', 'undefined', 'true', 'null'], 'correct_index' => 2, 'explanation' => 'Koşul blokları sadece içlerindeki ifade true (doğru) olarak değerlendirildiğinde çalışır.', 'difficulty' => 'easy'],
                ['body' => 'JavaScript\'te katı eşitlik (hem değer hem de tip kontrolü) hangi operatörle yapılır?', 'options' => ['=', '==', '===', '=>'], 'correct_index' => 2, 'explanation' => '`===` operatörü katı eşitliktir. `==` ise tipleri eşitlemeye çalışarak kontrol eder.', 'difficulty' => 'medium'],
                ['body' => '`true && false` ifadesinin sonucu nedir?', 'options' => ['true', 'false', 'undefined', '1'], 'correct_index' => 1, 'explanation' => 'Ve (&&) operatöründe sonucun true çıkması için iki tarafın da true olması gerekir.', 'difficulty' => 'easy'],
                ['body' => '`if (0)` yazıldığında bu blok çalışır mı?', 'options' => ['Çalışır', 'Çalışmaz', 'Sonsuz döngüye girer', 'Hata verir'], 'correct_index' => 1, 'explanation' => 'JavaScript\'te 0, "falsy" (yanlış kabul edilen) bir değerdir. Bu nedenle if bloğu çalışmaz.', 'difficulty' => 'hard'],
                ['body' => 'Birden fazla koşulu zincirlemek için hangi yapı kullanılır?', 'options' => ['if - else', 'if - else if - else', 'switch - for', 'while - do'], 'correct_index' => 1, 'explanation' => 'Art arda koşullar için `else if` yapısı kullanılır.', 'difficulty' => 'medium'],
            ],
            'coding_tasks' => [
                [
                    'title' => 'Sayı İşaretçisi',
                    'description' => 'Verilen sayının pozitif, negatif veya sıfır olduğunu bulan `isaretBul` fonksiyonunu yaz. Dönüş değerleri tam olarak "Pozitif", "Negatif" veya "Sıfır" (ilk harfi büyük string) olmalıdır.',
                    'starter_code' => "function isaretBul(sayi) {\n  // Kodunu buraya yaz\n  \n}",
                    'hint' => 'if (sayi > 0) ile başlayıp else if ve else ile devam edebilirsin.',
                    'test_cases' => [
                        'function' => 'isaretBul',
                        'cases' => [
                            ['args' => [5], 'expected' => 'Pozitif'],
                            ['args' => [-3], 'expected' => 'Negatif'],
                            ['args' => [0], 'expected' => 'Sıfır'],
                            ['args' => [100], 'expected' => 'Pozitif']
                        ]
                    ]
                ]
            ]
        ],
        ['name' => 'Döngüler Denizi', 'slug' => 'loops-sea', 'icon' => '↻', 'x' => 48, 'y' => 80, 'description' => 'Aynı işlemleri binlerce kez yorulmadan yapabilen döngü gemileriyle bu denizi aş.'],
        ['name' => 'Diziler Dağı', 'slug' => 'arrays-mountain', 'icon' => '[ ]', 'x' => 65, 'y' => 62, 'description' => 'Çoklu verileri tek bir listede tutmayı öğrenerek dağın zirvesine tırman.'],
        ['name' => 'Fonksiyonlar Ormanı', 'slug' => 'functions-forest', 'icon' => 'ƒ', 'x' => 50, 'y' => 45, 'description' => 'Tekrar eden kodları bir araya toplayıp onlara isimler ver. Kendi araçlarını yarat!'],
        ['name' => 'Nesneler Adası', 'slug' => 'objects-island', 'icon' => '{ }', 'x' => 30, 'y' => 35, 'description' => 'Gerçek dünyadaki nesneleri özellikleri ve yetenekleriyle koda aktar.'],
        ['name' => 'Hata Ayıklama Mağarası', 'slug' => 'debugging-cave', 'icon' => 'bug', 'x' => 55, 'y' => 22, 'description' => 'Karanlıkta gizlenen böcekleri (bug) bul ve ez. Console senin en iyi fenerin olacak.'],
        ['name' => 'Algoritma Kalesi', 'slug' => 'algorithms-castle', 'icon' => '⚙', 'x' => 80, 'y' => 12, 'description' => 'Sadece kod yazmak yetmez, en hızlı ve verimli yolu bulmalısın. Zirveye ulaştın!'],
    ]
];
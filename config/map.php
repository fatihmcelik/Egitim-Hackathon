<?php

return [
    // Harita zemini ve sis (public klasörüne göre). Dosya yoksa CSS zemin kullanılır.
    // Görselin uzantısı .jpg veya .webp ise burayı değiştir.
    'background' => 'images/map-bg.png',
    'fog'        => 'images/fog.png',

    // Her bölge kartının merkezi: [soldan %, yukarıdan %].
    // Haritadaki 5 adanın üstüne denk gelecek şekilde ayarlanır (şimdilik tahmini).
    'slots' => [
        [33, 55],
        [53, 34],
        [80, 46],
        [50, 80],
        [82, 84],
    ],
];
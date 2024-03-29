### Memcached vs Redis

#### Memcached

Memcached - ПО, предоставляющее сервис для кэширования данных в ОЗУ на основе хэш-таблицы.

Плюсы:

1. Простой в использовании, тратит меньше памяти для метаданных;
2. Многопоточный;
3. Хорошо масштабируется вертикально;

Минусы:

1. Поддерживает единственный тип данных - строки;
2. Сложности с персистентностью и, как следствие, с надёжностью: сброс данных на диск не работает из коробки, приходится использовать сторонние решения;
3. Нет механизмов кластеризации из коробки;
4. Максимальный объём одной записи составляет 128Мб;
5. Проблемы с фрагментацией памяти при хранении данных разной величины (для распределения памяти используется slab-аллокатор, стремящийся сократить внутреннюю фрагментацию при выделении памяти путём использования блоков (slab) для хранения данных опеределённой длины);

Особенности:

1. Поддерживает работу с тэгами (присвоение тэгов записи, версионирование тэгов, сброс записей по тэгам);
2. Можно масштабировать горизонтально с помощью mcrouter от Facebook;

#### Redis

Redis - резидентная система управления базами данных класса NoSQL с открытым исходным кодом, работающая со структурами данных типа «ключ — значение». Используется как для баз данных, так и для реализации кэшей, брокеров сообщений.

Плюсы:

1. Поддерживает множество типов данных: строки, спсики, множества, хэш-таблицы, упорядоченные множества;
2. Обеспечивает персистентность данных путём сброса на диск снимков данных и журналирования;
3. Хорошо масштабируется горизонтально и имеет готовую систему Redis Sentiel для управления кластером, поддерживает репликацию;
4. Поддерживает транзакции и пакетную обработку;
5. Может использоваться для реализации обмена сообщениями в модели "издатель-подписчик";
6. Максимальный объём строки - 512Мб, максимальный объём списка, множества и хэш-таблицы - 2^32-1 записей;

Минусы:

Особенности:

#### Общие особенности

1. Реализация кэша;
2. Поддержка атомарных операций;
3. Возможность масштабирования;

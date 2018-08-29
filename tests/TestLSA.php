<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use  PHPLsa\LSA;

/**
 * Class TestLSA
 */
class TestLSA extends TestCase
{
    /**
     * @return LSA
     */
    public function testInitLsa() {
        $keyWords = ['quick', 'brown', 'fox', 'jumped', 'lazy', 'dog', 'hey', 'diddle', 'cat',
                     'fiddle', 'cow',  'moon', 'little', 'laughed', 'fun',  'dish', 'ran',
                     'away', 'spoon',  ];
        $transformText = new \PHPLsa\TransformTextByKeyWord($keyWords);
        $trans = $transformText->transform(['ABC']);
        $this->assertTrue(count($keyWords) == count($trans));

        $lsa = new LSA(4);
        $lsa->setTextTransformer($transformText);

        return $lsa;
    }

    /**
     * @param LSA $lsa
     * @depends testInitLsa
     */
    public function testFit(LSA $lsa) {
        $documents = [
            "The quick brown fox jumped over the lazy dog",
            "hey diddle diddle, the cat and the fiddle",
            "the cow jumped over the moon",
            "the little dog laughed to see such fun",
            "and the dish ran away with the spoon",
        ];

        $trans = $lsa->fitTransform($documents);
        $query = "the brown fox ran around the dog";
        $index = $lsa->query($query, $trans);
       // echo $documents[$index], PHP_EOL;

        $this->assertTrue($index == 0);
    }


    /**
     *
     */
    public function testRu() {
        $documents = [
            'В список таких мест попал Эрмитаж – в подвалах музея сейчас живут 50-60 кошек и котов.  Также любителям кошек рекомендуется побывать в доме-музее Хэмингуэя во Флориде, где живут 57 котов, в Музее кошки в Амстердаме и в литовском Музее кошек в Шяуляе – там собрано около 10 тысяч экспонатов со всего мира: скульптуры, фотографии, витражи с изображением кошек.',
            'Имена для кошек подбираются хозяевами тщательно и обдуманно. Некоторые размышляют над именем еще до появления в доме пушистого комочка. Как назвать кота, приходит в голову и при первой встрече с новым членом семьи. Кошачьи имена подбирают исходя из характера, внешнего вида и даже гастрономических предпочтений питомцев.',
            'Так, самыми популярными породами кошек у россиян стали шотландские и британские, средняя цена которых составила 2,8 тысячи и 3,2 тысячи рублей соответственно. В пятерку также вошли мейн-куны (8,5 тысячи рублей), бенгальские...',
            'В Салавате живёт мастерица, которая шьёт мягкие игрушки по технологии Тедди. Они были особенно популярны в 19-20 веках, но и сейчас их любят и дети, и взрослые. Какие секреты хранит старый плюшевый медведь, расскажет Инна Горина.',
            'Красивую благотворительную акцию устроили болельщики голландского футбольного клуба «Фейеноорд» во время матча чемпионата Нидерландов по футболу против «Эксельсиора». При счете 1:0 зрители верхнего яруса забросали мягкими игрушками сектор с детьми из госпиталя Erasmus Sofia',
        ];


        $lsa = new LSA(4);
        $trans = $lsa->fitTransform($documents);
        $this->assertTrue(!empty($trans));

        $query = "Тедди просто секси";
        $index = $lsa->query($query, $trans);
        //echo $documents[ $index];
        $this->assertTrue($index == 3);
    }
}
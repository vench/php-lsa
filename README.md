# PHP LSA Library

Singular value decomposition in PHP implementation language.

This package can analyze texts to find a relation with given terms.

It can take an array with text document strings and transform them so they can be queried.

The package can also take a given text and perform a query to return the index of the document that matches better text that was given in the original array of text documents.

#### Install

```composer require vench/php-lsa```

#### Examples

Find the most similar text.
```
    $documents = [
            "The quick brown fox jumped over the lazy dog",
            "hey diddle diddle, the cat and the fiddle",
            "the cow jumped over the moon",
            "the little dog laughed to see such fun",
            "and the dish ran away with the spoon",
     ];

     $lsa = new LSA(4);
     $trans = $lsa->fitTransform($documents);
     
     $query = "the brown fox ran around the dog";
     $index = $lsa->query($query, $trans);
     echo $documents[$index], PHP_EOL;

```

#### TODO
- [x] add save data
- [x] add load data
- [x] change transform

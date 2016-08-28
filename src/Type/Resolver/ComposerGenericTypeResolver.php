<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Resolver;

 use NicMart\Generics\Composer\DirectoryResolver;
 use NicMart\Generics\Name\Context\NamespaceContextExtractor;
 use NicMart\Generics\Name\FullName;
 use NicMart\Generics\Name\Name;
 use NicMart\Generics\Type\GenericType;
 use NicMart\Generics\Type\ParametrizedType;
 use NicMart\Generics\Type\Parser\TypeParser;
 use NicMart\Generics\Type\Serializer\TypeSerializer;
 use NicMart\Generics\Type\SimpleReferenceType;
 use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;
 use NicMart\Generics\Type\Type;
 use SplFileInfo;
 use Symfony\Component\Finder\Finder;

 /**
  * Class ComposerGenericTypeResolver
  * @package NicMart\Generics\Type\Resolver
  */
 class ComposerGenericTypeResolver implements GenericTypeResolver
{
    /**
     * @var DirectoryResolver
     */
    private $directoryResolver;

    /**
     * @var TypeSerializer
     */
    private $typeSerializer;
    /**
     * @var NamespaceContextExtractor
     */
    private $contextExtractor;
     /**
      * @var TypeParser
      */
     private $typeParser;

     /**
      * GenericNameResolver constructor.
      * @param TypeSerializer $typeSerializer
      * @param TypeParser $typeParser
      * @param DirectoryResolver $directoryResolver
      * @param NamespaceContextExtractor $contextExtractor
      */
    public function __construct(
        TypeSerializer $typeSerializer,
        TypeParser $typeParser,
        DirectoryResolver $directoryResolver,
        NamespaceContextExtractor $contextExtractor
    ) {
        $this->directoryResolver = $directoryResolver;
        $this->typeSerializer = $typeSerializer;
        $this->contextExtractor = $contextExtractor;
        $this->typeParser = $typeParser;
    }

    /**
     * @param ParametrizedType $parametrizedType
     * @return GenericType
     */
    public function toGenericType(ParametrizedType $parametrizedType)
    {
        $mainName = $parametrizedType->name();

        $nsName = $mainName->up();

        $dirs = $this->directoryResolver->directories($nsName->toString());

        $finder = new Finder();

        $atLeasteOneDirExists = false;

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $atLeasteOneDirExists = true;
                $finder->in($dir);
            }
        }

        if (!$atLeasteOneDirExists) {
            $this->unableToResolve($parametrizedType);
        }
        
        $finder->name($this->regexp($parametrizedType));

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $name = $nsName->down($file->getBasename(".php"));
            if (!$this->isGeneric($name)) continue;

            $context = $this->contextExtractor->contextOf(
                file_get_contents($file->getPathname())
            );

            $parsedType = $this->typeParser->parse(
                $name,
                $context
            );

            $this->assertGenericType($parsedType, $name, $file);

            return $parsedType;
        }

        $this->unableToResolve($parametrizedType);
    }

    /**
     * @param FullName $name
     * @return mixed
     */
    private function isGeneric(FullName $name)
    {
        try {
            return is_a($name->toString(), '\NicMart\Generics\Generic', true);
        } catch (\Exception $e) {
            return false;
        }
    }

     /**
      * A bit hackish here, we are passing regexps as names to have
      * the serializer bulding the regexp for us
      *
      * @param ParametrizedType $parametrizedType
      * @return mixed
      */
    private function regexp(ParametrizedType $parametrizedType)
    {
        $paramRegexpType = new SimpleReferenceType(
            FullName::fromString(".+")
        );

        $regexpType = $parametrizedType->map(new ByCallableTypeTransformer(
            function () use ($paramRegexpType) {
                return $paramRegexpType;
            }
        ));

        $genericRegexpFullName = $this->typeSerializer->serialize(
            $regexpType
        );

        return sprintf(
            "/^%s\.php$/",
            $genericRegexpFullName->last()->toString()
        );
    }

     /**
      * @param $parsedType
      * @param $name
      * @param $file
      */
     private function assertGenericType(Type $parsedType, Name $name, SplFileInfo $file)
     {
         if (!$parsedType instanceof GenericType) {
             throw new \RuntimeException(sprintf(
                 "Type Name '%s' from file '%s' has not been parsed to a Generic Type."
                 . "It has been parsed to '%s' of type '%s'.",
                 $name->toString(),
                 $file->getPathname(),
                 $this->typeSerializer->serialize($parsedType)->toString(),
                 get_class($parsedType)
             ));
         }
     }

     /**
      * @todo Use a custom Domain Exception
      * @param ParametrizedType $parametrizedType
      */
     private function unableToResolve(ParametrizedType $parametrizedType)
     {
         throw new \UnderflowException(sprintf(
             "Unable to resolve Generic Type file for parametrized type %s",
             $this->typeSerializer->serialize($parametrizedType)->toString()
         ));
     }
 }
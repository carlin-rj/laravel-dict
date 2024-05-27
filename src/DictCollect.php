<?php
namespace Carlin\LaravelDict;

use Carlin\LaravelDict\Attributes\EnumClass;
use Carlin\LaravelDict\Attributes\EnumProperty;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionException;
use Throwable;

class DictCollect
{
    /**
     * @throws ReflectionException
     */
    public function collect(array $paths): array
    {
        $dict = [];
        foreach ($paths as $path){
            $files = glob($path);
            foreach ($files as $file) {
                $class = $this->getClassByFile($file);
                if (! $class) {
                    continue;
                }
                $reflectionClass = new ReflectionClass($class);
                $attribute = $reflectionClass->getAttributes(EnumClass::class);
                if (empty($attribute)) {
                    continue;
                }
                /** @var EnumClass $attribute */
                $attribute = $attribute[0]->newInstance();
                $dict[$class]['name'] = $attribute->name;
                $dict[$class]['description'] = $attribute->description;
				if ($attribute->group) {
					$dict[$class]['group'] = $attribute->group;
				}
				$dict[$class] += $attribute->options;
                $dict[$class]['data'] = $this->collectProperty($reflectionClass);
                $dict[$class]['class'] = $class;
            }
        }

        return $dict;
    }

	private function getClassByFile(string $file): ?string
	{
		$code = file_get_contents($file);


		try {
			$parser = (new ParserFactory())->createForNewestSupportedVersion();
			// 解析 PHP 代码
			$ast = $parser->parse($code);
			$traverser = new NodeTraverser();

			// 添加命名空间解析器
			$traverser->addVisitor(new NameResolver());

			// 添加自定义访问者来提取类名
			$classExtractor = new class extends NodeVisitorAbstract {
				public string|null $className = null;

				public function enterNode(Node $node): void {
					// 如果节点是类定义
					if ($node instanceof Node\Stmt\Class_) {
						// 判断是否包含命名空间
						if (isset($node->namespacedName)) {
							$this->className = $node->namespacedName->toString();
						} else {
							$this->className = $node->name->toString();
						}
					}
				}
			};
			$traverser->addVisitor($classExtractor);

			// 遍历抽象语法树
			$traverser->traverse($ast);

			// 获取提取的类名
			return $classExtractor->className;

		} catch (Throwable) {
			// 忽略解析错误
			return null;
		}
	}

    public function collectProperty(ReflectionClass $reflectionClass): array
    {
        $map = [];
        foreach ($reflectionClass->getReflectionConstants() as $constant) {
            $attributes = $constant->getAttributes(EnumProperty::class);
            foreach ($attributes as $attribute) {
                /** @var EnumProperty $enumAttribute */
                $enumAttribute = $attribute->newInstance();
                $data = [
                    'name' => $enumAttribute->description,
                    'code' => $constant->getValue(),
                ];
                $map[] = $data + $enumAttribute->options;
            }
        }

        return $map;
    }
}

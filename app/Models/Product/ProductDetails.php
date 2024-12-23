<?php

declare(strict_types=1);

namespace App\Models\Product;

use App\Models\ExtendedDetails\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetailsVideo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use App\Helpers\FixEncoding;
final class ProductDetails extends Model
{
	use HasFactory;


	public const TYPE_CONFIGURABLE = 'configurable';

    /**
     * @var string
     */
    public const TYPE_SIMPLE = 'simple';

    /**
     * @var mixed|string
     */
    public mixed $description;

    /**
     * @var mixed|string
     */
    public mixed $title;

    protected $fillable = [
		'product_id',
		'parent_product_id',
		'type',
		'title',
		'score',
		'rating',
		'price',
		'shipping_price',
		'brand',
		'breadcrumbs_flat',
		'image',
		'description',
		'vendor',
		'html',
		'has_variants',
		'engine',
        'has_combinations',
        'combination_separator'
	];

	//endregion

	public function variants(): HasMany
	{
		return $this->hasMany(Variant::class);
	}

	public function categories(): HasMany
	{
		return $this->hasMany(Category::class);
	}

	public function thumbnails(): HasMany
	{
		return $this->hasMany(Thumbnail::class, 'product_details_id');
	}

    public function combinations(): HasMany
    {
        return $this->hasMany(VariantCombination::class, 'product_details_id');
    }

    public function extendedDetails(): HasOne
    {
        return $this->hasOne(ExtendedDetails::class, 'product_details_id');
    }

    public function videos(): HasOne
    {
        return $this->hasOne(ExtendedDetailsVideo::class);
    }

	public function getData(): array
	{
		$variants = [];
        $combinations = [];
		$categories = [];
		$thumbnails = [];

        $type = $this->getAttribute('type');
		$hasVariants = $this->getAttribute('has_variants');
        $hasCombinations = $this->getAttribute('has_combinations');

		$result = [
			'productId' => $this->getAttribute('product_id'),
			'parent_product_id' => $this->getAttribute('parent_product_id'),
			'type' => $type,
			'has_variants' => $hasVariants,
            'has_combinations' => $hasCombinations,
            'combination_separator' => $this->getAttribute('combination_separator'),
			'vendor' => $this->getAttribute('vendor'),
			'image' => $this->getAttribute('image'),
			'title' => FixEncoding::fix($this->getAttribute('title') ?? ''),
			'brand' => FixEncoding::fix($this->getAttribute('brand') ?? ''),
			'price' => $this->getAttribute('price'),
			'breadcrumbs_flat' => FixEncoding::fix($this->getAttribute('breadcrumbs_flat') ?? ''),
			'shipping_price' => $this->getAttribute('shipping_price'),
			'score' => $this->getAttribute('score'),
			'rating' => $this->getAttribute('rating'),
			'description' => FixEncoding::fix($this->getAttribute('description') ?? ''),
            'engine' => $this->getAttribute('engine'),
		];

		if ($type === self::TYPE_CONFIGURABLE) {
            if ($hasVariants) {
                foreach ($this->getRelation('variants')  as $variant) {
                    $variants[] = $variant->getData();
                }
                foreach ($this->getRelation('combinations') as $combination) {
                    $combinations[] = $combination->getData();
                }
            }
		}

        $extendedDetails = [];

        if(isset($this->getRelations()["extendedDetails"])){
            $extendedDetails = $this->getRelation('extendedDetails');
        }

        foreach ($this->getRelation('categories')  as $category) {
			$categories[] = $category->getData();
		}

		$result['categories'] = $categories;

		/*foreach ($this->thumbnails()->get() as $thumbnail) {
			$thumbnails[] = $thumbnail->getData();
		}*/

        foreach ($this->getRelation('thumbnails') as $thumbnail) {
            $thumbnails[] = $thumbnail->getData();
        }

		$result['thumbnails'] = $thumbnails;
        $result['variants'] = $variants;
        $result['combinations'] = $combinations;

        if($extendedDetails){
            foreach($extendedDetails->getAttributes() as $key => $value){
                $result[$key] = $value;
            }

            if(isset($extendedDetails->getRelations()["videos"])){
                foreach ($extendedDetails->getRelation('videos') as $video) {
                    if(is_object($video)){
                        $result['videos'][] = $video;
                    }

                }
            }else{
                foreach ($extendedDetails->videos()->get() as $video) {
                    foreach($video->videos()->get() as $v){
                        if(is_object($video)) {
                            $result['videos'][] = $v;
                        }
                    }
                }
            }
            //$extendedDetails->getRelation('videos')->getRelation('video')
        }

		return $result;
	}
	public function store()
	{
		DB::transaction(function () {
			$this->save();

            foreach ($this->getRelation('categories') as $category) {
                $this->categories()->save($category);
            }
            foreach ($this->getRelation('thumbnails') as $thumbnail) {
                $this->thumbnails()->save($thumbnail);
            }

            foreach ($this->getRelation('variants') as $variantsGroup) {
                $variantsGroup->setAttribute('parent_product_id', $this->getAttribute('id'));
                $this->variants()->save($variantsGroup);
                foreach ($variantsGroup->getRelation('options') as $option) {
                    $option->variant_id = $variantsGroup->id;
                    $option->save();
                }
            }
            foreach ($this->getRelation('combinations') as $combination) {
                $combination->setAttribute('product_details_id', $this->getAttribute('id'));
                $combination->save();
            }
		});
	}
}

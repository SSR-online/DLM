<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Auth;

class Node extends Model {
	use Traits\HasSettings;
	
	protected $casts = [ 'settings' => 'array' ];
	// protected $with = ['parent'];

	protected $dispatchesEvents = [
        'deleting' => Observers\NodeObserver::class,
    ];

	//TODO: Autogenerate this from list of block types?
	private $allowed_types = [
		'QuestionBlock' => 'question',
		'QuizBlock' => 'quiz',
		'TextBlock' => 'text',
		'DetailsBlock' => 'details',
		'AsideBlock' => 'aside',
		'LinkBlock' => 'link',
		'ImageBlock' => 'image',
		'FileBlock' => 'file',
		'H5PBlock' => 'h5p',
		'DiscussionBlock' => 'discussion'
	];

	private $descendants_attribute;

   	public function module() {
	        return $this->belongsTo('App\Module');
	}

	public function block() {
		return $this->morphTo();
	}

	public function parent() {
		return $this->belongsTo('App\Node', 'parent_id');
	}

	public function layouts() {
		return $this->hasMany('App\Layout');
	}

	public function layoutSlot() {
		return $this->belongsTo('App\LayoutSlot');
	}

	public function children() {
		return $this->hasMany('App\Node', 'parent_id')->orderBy('sort_order');
	}

	public function previous() {
	 	return $this->belongsTo('App\Node', 'previous_id');
	}

	public function duplicate($parent = null, $layout_slot = null) {
		if($parent == null) { $parent = $this->parent; }
		$replicant = $this->replicate();
		$replicant->save();
		if($parent) {
			$replicant->parent()->associate($parent);
		}
		if($layout_slot) {
			$replicant->layoutSlot()->associate($layout_slot);
		}
		if($this->block) {
			$replicant_block = $this->block->duplicate();
			$replicant_block->save();
			$replicant->block()->associate($replicant_block);
		}
		// Keep track of old and new layout slots, to put children 
		// in the correct slot.
		$slot_map = []; 
		if($this->layouts) {
			foreach($this->layouts as $layout) {
				$layout_replicant = $layout->replicate();
				$layout_replicant->save();
				foreach($layout->slots as $slot) {
					$slot_replicant = $slot->replicate();
					$slot_replicant->layout()->associate($layout_replicant);
					$slot_replicant->save();
					$slot_map[$slot->id] = $slot_replicant;
				}
				$replicant->layouts()->save($layout_replicant);
			}
		}
		$this->children->each(function($child, $key) use($replicant, $slot_map) {
			$layout_slot = null;
			if($child->layoutSlot) {
				Log::info('layout slot was' . $child->layoutSlot->id);

				//Not all children have a slot in this layout, since they
				//can be pages with their own layout (and slot)
				if(array_key_exists($child->layoutSlot->id, $slot_map)) {
					$layout_slot = $slot_map[$child->layoutSlot->id];
					Log::info('layout slot will be' . $layout_slot->id);
				}
			}
			$child->duplicate($replicant, $layout_slot);
		});
		$replicant->save();
	}

	/**
	 * Gets the previous node in the logical flow
	 * @param  null $value 
	 * @return App\Node the previous node in this flow
	 * 
	 * The previous node can be:
	 * - manually set
	 * - none
	 * - automatically found, options are
	 * -- Same level: the previous node in this level
	 * -- Higher level: For the first node in this level,
	 *    select the parent node
	 */
	public function getPreviousAttribute( $value ) {
		if($this->previous_id > 0) {
			return $this->previous()->first();
		} else {
			if($this->previous_id == -1) {
				return false;
			} else if($this->previous_id == -2) {
				$the_node = false;

				if($this->parent_id && $this->parent) {
					$nodes = $this->parent->children->filter(function($child_node, $key) {
						return $child_node->is_page; //everything on this level
					});
				} else {
					$nodes = $this->module->nodes()->whereNull('parent_id')->get(); //All top level nodes
				}
				if($nodes) {
					foreach($nodes as $node) {
						//If we found a node, and we're at $this node, return previous
						if($node->is($this) && $the_node) {
							return $the_node;
						}
						$the_node = $node;
					}
					//Nothing found? Return the parent node
					return $this->parent;
				}
			}
		}
		return false;
	}

	public function getPreviousTitleAttribute( $value ) {
		if(!empty($this->setting('previous_title'))) {
			return $this->setting('previous_title');
		} else {
			return optional($this->previous)->title;
		}
	}

	public function next() {
		return $this->belongsTo('App\Node', 'next_id');
	}

	/**
	 * Gets the next node in logical flow
	 * @param  null $value Null value
	 * @return App\Node A node
	 * 
	 * See getPreviousAttribute for info
	 */
	public function getNextAttribute( $value ) {
		if($this->next_id > 0) {
			return $this->next()->first();
		} else {
			if($this->next_id == -1) {
				return false;
			} else if($this->next_id == -2) {
				$return_next = false;
				$child_pages = $this->children->filter(function($child_node, $key) {
					return $child_node->is_page;
				});
				if(count($child_pages) > 0) {
					return $child_pages->first(); // First child node that is a page
				}
				else if($this->parent) {
					$nodes = $this->parent->children->filter(function($child_node, $key) {
						return $child_node->is_page;
					}); // All nodes on this level
				} else {
					$nodes = $this->module->nodes()->whereNull('parent_id')->get(); //All top level nodes
				}
				if($nodes) {
					foreach($nodes as $node) {
						if($return_next) { 
							return $node;
						}
						if($node->is($this)) {
							$return_next = true;
						}
					}
					if($this->parent_id && $this->parent) {
						$nodes = ($this->parent->parent) ? $this->parent->parent->children : $this->module->nodesByParent()[''];
						$return_next = false;

						foreach($nodes as $node) {
							if($return_next) { 
								return $node;
							}
							if($node->is($this->parent)) {
								$return_next = true;
							}
						}
					}
				}
			}
		}
		return false;
	}

	public function getNextTitleAttribute( $value ) {
		if(!empty($this->setting('next_title'))) {
			return $this->setting('next_title');
		} else {
			return optional($this->next)->title;
		}
	}

	public function getIsPageAttribute( $value ) {
		if($value == true) { return true; }
		else {
			if($this->parent == null) { return true; }
		}
		return false;
	}

	//Return the url path to this node
	//Top nodes are pages, subnodes are anchors
	//
	// TODO: Stop at nearest 'page'
	public function path() {
		//Find the nearest page ancestor of this node
		if(!$this->is_page) {
			$ancestors = $this->ancestors($this);
			// if($this->id == 6494) { foreach($ancestors as $ancestor) dump($ancestor); }
			$root = $ancestors->first(function($value, $key) {
				return $value->is_page == true;
			});
			return '/module/' . $this->module->id . '/' . $root->id . '#node-' . $this->id;
		} else {
			return '/module/' . $this->module->id . '/' . $this->id;
		}
	}

	//Get a css classname (list) for this node
	public function classString() {
		$classes = [];
		if($this->block) {
			if(array_key_exists(class_basename($this->block), $this->allowed_types)) {
				$classes[] = $this->allowed_types[class_basename($this->block)];
			}
		}
		if( !empty($this->block->classList) ) {
			$classes = array_merge($classes, explode(' ', trim($this->block->class_list)));
		}
		if( !empty(session('ismoving')) && session('ismoving') == $this->id) {
			$classes[] = 'isbeingmoved';
		}
		return implode(' ', $classes);
	}

	public function has_ancestor($node) {
		$ancestors = $this->ancestors($this);
		return $ancestors->whereIn('id', $node->id)->isNotEmpty();
	}

	public function ancestors($node) {
		$ancestors = collect([$node]);
		if($node->parent) {
			return $ancestors->merge($this->ancestors($node->parent));
		} else {
			return $ancestors;
		}
	}

	/**
	 * Gets the first ancestor of this node that's a page
	 * @return Node       The first ancestor node that's a page.
	 */
	public function page() {
		foreach($this->ancestors($this) as $node) {
			if($node->is_page) { return $node; }
		}
	}

	public function getDescendantsAttribute( $value ) {
		if(!$this->descendants_attribute) {
			$this->descendants_attribute = $this->descendants();
		}
		return $this->descendants_attribute;
	}

	public function has_descendant($node) {
		$descendants = $this->descendants;
		return $descendants->whereIn('id', $node->id)->isNotEmpty();
	}

	public function descendants() {
		$descendants = $this->children->load(['children', 'block']);
		if($this->children) {
			foreach($this->children as $child) {
				if($this->child->id == $this->id) { continue; } //Prevent running through circular references
				$descendants = $descendants->merge($child->descendants());
			}
		}
		return $descendants;
	}

	public function getShowInMenuAttribute( $value ) {		
		return $this->setting('show_in_menu');
		// if(!$this->parent()) {
		// 	$this->add_setting('show_in_menu', true);
		// 	return true;
		// }
		// return false; 
	}

	protected function filter_setting($key, $value) {
		if($key == 'jump_nodes') {
			$collection = collect($value)->filter(function ($value) {
				return $value['id'] > 0;
			});
			$value = $collection->toArray();
		}
		return $value;
	}

	public function getCompletedAttribute( $value ) {
		// Check if this is a page, if so, check if it has quizzes in its list. Pages
		// with quizzes only complete when the quiz is complete.
		if($this->is_page) {
			$quizzes = [];
			$completed = true;
			foreach($this->descendants as $descendant) {
				if($descendant->block && class_basename($descendant->block) == 'QuizBlock') {
					$completed = false;
					//Check if at least one attempt was completed
					foreach($descendant->block->current_user_attempts() as $attempt) {
						if($attempt->complete) { 
							$completed = true;
							break;
						}
					}
					if(!$completed) { return false; }
				}
			}
		}
		// Check if the user has seen this page.
		if($nodes_seen = Auth::user()->setting('nodes_seen')) {
			if(Auth::user() && in_array($this->id, $nodes_seen)) { return true; }
		}
		if(!$this->is_page && $this->show_in_menu) {
			$ancestors = $this->ancestors($this);
			$parent_seen = $ancestors->last(function($value, $key) use ($nodes_seen) {
				if(Auth::user() && in_array($value->id, $nodes_seen)) { return true; }
			});
			if($parent_seen) { return true; }
		}
	}

	public function getChildTypesAttribute() {
		if($this->block) {
			return $this->block->childTypes;
		} else {
			return [
				'App\QuestionBlock',
				'App\QuizBlock',
				'App\TextBlock',
				'App\DetailsBlock',
				'App\LinkBlock',
				'App\ImageBlock',
				'App\AsideBlock',
				'App\VideoBlock',
				'App\H5PBlock',
				'App\FileBlock',
				'App\DiscussionBlock'
			];

		}
	}
}

pub mod song;

use serde_json::Value;

pub trait FromValue {
    fn from(value: Value) -> Option<Self>;
}

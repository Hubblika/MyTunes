use crate::{types::FromValue, utils::fetch};
use reqwest::Method;
use serde::{Deserialize, Serialize};

#[derive(Clone, Debug, Serialize, Deserialize)]
pub struct Song {
    pub uuid: String,
    // pub title: String,
    // pub artist: String,
    // pub url: String,
    // pub cover_url: String,
    // pub date: String,
    // pub duration: String,
}

impl FromValue for Song {
    fn from(value: serde_json::Value) -> Option<Self> {
        Some(Self {
            uuid: value.get("")?.as_str()?.into()
        })
    }
}

impl PartialEq for Song {
    fn eq(&self, other: &Self) -> bool {
        self.uuid == other.uuid
    }
}

impl Song {
    pub async fn by_uuid(uuid: String) -> Option<Self> {
        fetch(Method::GET, format!("/api/song/{}", uuid)).await
    }
}
